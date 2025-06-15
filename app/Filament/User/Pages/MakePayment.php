<?php

namespace App\Filament\User\Pages;

use App\Models\Book;
use App\Models\Payment;
use App\Models\Transaction;
use App\Settings\PaymentSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yabacon\Paystack;
use App\Enums\BorrowedStatus;

class MakePayment extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Make Payment';
    protected static ?string $title = 'Make Payment';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.make-payment';

    public ?array $data = [];

    public function mount(): void
    {
        if (!Auth::check() || Auth::user()->role->name !== 'borrower') {
            redirect()->route('filament.user.pages.dashboard');
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('transaction_id')
                    ->label('Select Transaction')
                    ->options(function () {
                        return Transaction::where('user_id', Auth::id())
                            ->whereIn('status', [BorrowedStatus::Borrowed, BorrowedStatus::Delayed])
                            ->with('book')
                            ->get()
                            ->mapWithKeys(function ($transaction) {
                                $label = $transaction->book->title;
                                if ($transaction->status === BorrowedStatus::Delayed) {
                                    $label .= ' (Overdue)';
                                } else {
                                    $label .= ' (Currently Borrowed)';
                                }
                                return [$transaction->id => $label];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        $set('payment_type', null);
                        $set('amount', null);
                    }),
                Select::make('payment_type')
                    ->label('Payment Type')
                    ->options(function ($get) {
                        $transactionId = $get('transaction_id');
                        if (!$transactionId) {
                            return [];
                        }

                        $transaction = Transaction::find($transactionId);
                        if (!$transaction) {
                            return [];
                        }

                        $options = [];
                        if ($transaction->status === BorrowedStatus::Delayed) {
                            $options['late_return'] = 'Late Return Payment';
                        }
                        $options['lost_book'] = 'Lost Book Payment';

                        return $options;
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, $set, $get) {
                        if ($state) {
                            $transaction = Transaction::with('book')->find($get('transaction_id'));
                            if ($transaction) {
                                if ($state === 'late_return') {
                                    $dueDate = $transaction->borrowed_date->addDays($transaction->borrowed_for);
                                    $daysOverdue = now()->isAfter($dueDate) ? $dueDate->diffInDays(now()) : 0;
                                    $dailyRate = (float) config('library.late_return_daily_rate', 100);
                                    $amount = $daysOverdue * $dailyRate;
                                } else {
                                    $multiplier = (float) config('library.lost_book_multiplier', 2);
                                    $amount = $transaction->book->price * $multiplier;
                                }
                                $set('amount', (string) $amount);
                            }
                        }
                    }),
                TextInput::make('amount')
                    ->label('Amount (₦)')
                    ->numeric()
                    ->required()
                    ->prefix('₦')
                    ->readOnly()
                    ->default(0)
                    ->helperText('Amount is calculated based on payment type and transaction'),
            ])
            ->statePath('data')
            ->columns(1);
    }

    public function makePayment(): void
    {
        $data = $this->form->getState();

        // Debug the form data
        Notification::make()
            ->title('Debug Info')
            ->body('Form Data: ' . json_encode($data))
            ->info()
            ->send();

        if (empty($data['transaction_id']) || empty($data['payment_type']) || empty($data['amount'])) {
            Notification::make()
                ->title('Error')
                ->body('Please fill in all required fields.')
                ->danger()
                ->send();
            return;
        }

        $transaction = Transaction::findOrFail($data['transaction_id']);
        $amount = (float) $data['amount'];

        // Create payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'book_id' => $transaction->book_id,
            'amount' => $amount,
            'payment_type' => $data['payment_type'],
            'status' => 'pending',
            'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
        ]);

        try {
            $paystack = new Paystack(config('services.paystack.secret'));

            // Convert amount to kobo (multiply by 100)
            $amountInKobo = (int) ($amount * 100);

            $tranx = $paystack->transaction->initialize([
                'amount' => $amountInKobo,
                'email' => Auth::user()->email,
                'reference' => $payment->payment_reference,
                'callback_url' => route('payment.callback'),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'transaction_id' => $transaction->id,
                    'payment_type' => $data['payment_type'],
                ],
            ]);

            if ($tranx->status) {
                redirect()->to($tranx->data->authorization_url);
            } else {
                $payment->update(['status' => 'failed']);
                Notification::make()
                    ->title('Payment Failed')
                    ->body('Failed to initialize payment. Please try again.')
                    ->danger()
                    ->send();
            }
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed']);
            Notification::make()
                ->title('Payment Error')
                ->body('Payment initialization failed: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('submit')
                ->label('Proceed to Payment')
                ->action('makePayment')
                ->color('primary'),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'paystackPublicKey' => config('services.paystack.public'),
        ];
    }
}
