<?php

namespace App\Filament\User\Pages;

use App\Models\Book;
use App\Models\Payment;
use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MakePayment extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Make Payment';
    protected static ?string $title = 'Make Payment';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.make-payment';

    public ?array $data = [];

    public function mount(): void
    {
        abort_unless(Auth::check() && Auth::user()->role?->name === Role::IS_BORROWER, 403);
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('book_id')
                    ->label('Book')
                    ->options(Book::query()->pluck('title', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('payment_type')
                    ->label('Payment Type')
                    ->options([
                        'late_return' => 'Late Return',
                        'lost_book' => 'Lost Book',
                    ])
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount (NGN)')
                    ->numeric()
                    ->required()
                    ->prefix('₦')
                    ->helperText('Enter the amount in Naira'),
            ])
            ->statePath('data');
    }

    public function makePayment()
    {
        $data = $this->form->getState();

        // Create a payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'book_id' => $data['book_id'],
            'amount' => $data['amount'],
            'payment_type' => $data['payment_type'],
            'status' => 'pending',
            'payment_reference' => 'PAY-' . Str::random(10),
            'payment_details' => [],
        ]);

        // Initialize Paystack payment
        $paystack = new \Yabacon\Paystack(config('services.paystack.secret'));
        try {
            $tranx = $paystack->transaction->initialize([
                'amount' => $data['amount'] * 100, // Convert to kobo
                'email' => Auth::user()->email,
                'reference' => $payment->payment_reference,
                'callback_url' => route('payment.callback'),
            ]);

            // Store Paystack reference in payment details
            $payment->update([
                'payment_details' => [
                    'paystack_reference' => $tranx->data->reference,
                    'authorization_url' => $tranx->data->authorization_url,
                ],
            ]);

            // Redirect to Paystack payment page
            return redirect($tranx->data->authorization_url);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Payment initialization failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            return null;
        }
    }
}
