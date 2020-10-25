<?php

declare(strict_types=1);

namespace App\Orchid\Screens\User;

use App\Orchid\Layouts\User\UserEditLayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Password;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserProfileScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Profile';

    /**
     * Display header description.
     *
     * @var string
     */
    public $description = 'Basic information';

    /**
     * @var User
     */
    protected $user;

    /**
     * Query data.
     *
     * @param Request $request
     *
     * @return array
     */
    public function query(Request $request): array
    {
        $this->user = $request->user();

        return [
            'user' => $this->user,
        ];
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): array
    {
        return [
            DropDown::make(__('Settings'))
                ->icon('open')
                ->list([
                    ModalToggle::make(__('Change Password'))
                        ->icon('lock-open')
                        ->method('changePassword')
                        ->modal('password'),
                ]),

            Button::make(__('Save'))
                ->icon('check')
                ->method('save'),
        ];
    }

    /**
     * @return \Orchid\Screen\Layout[]
     */
    public function layout(): array
    {
        return [
            UserEditLayout::class,

            Layout::modal('password', [
                Layout::rows([
                    Password::make('old_password')
                        ->placeholder(__('Enter the current password'))
                        ->required()
                        ->title(__('Old password'))
                        ->help('This is your password set at the moment.'),

                    Password::make('password')
                        ->placeholder(__('Enter the password to be set'))
                        ->required()
                        ->title(__('New password')),

                    Password::make('password_confirmation')
                        ->placeholder(__('Enter the password to be set'))
                        ->required()
                        ->title(__('Confirm new password'))
                        ->help('A good password is at least 15 characters or at least 8 characters long, including a number and a lowercase letter.'),
                ]),
            ])
                ->title(__('Change Password'))
                ->applyButton('Update password'),
        ];
    }

    /**
     * @param Request $request
     */
    public function save(Request $request)
    {
        $request->validate([
            'user.name'  => 'required|string',
            'role.email' => [
                'required',
                Rule::unique(User::class, 'email')->ignore($request->user()),
            ],
        ]);

        $request->user()
            ->fill($request->get('user'))
            ->save();

        Toast::info(__('Profile updated.'));
    }

    /**
     * @param Request $request
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|password:web',
            'password'     => 'required|confirmed',
        ]);

        tap($request->user(), function ($user) use ($request) {
            $user->password = Hash::make($request->get('password'));
        })->save();

        Toast::info(__('Password changed.'));
    }
}
