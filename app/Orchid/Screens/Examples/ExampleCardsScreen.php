<?php

namespace App\Orchid\Screens\Examples;

use Illuminate\Http\Request;
use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Contracts\Cardable;
use Orchid\Screen\Layouts\Card;
use Orchid\Screen\Layouts\Compendium;
use Orchid\Screen\Layouts\Facepile;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ExampleCardsScreen extends Screen
{
    /**
     * Display header name.
     *
     * @var string
     */
    public $name = 'Cards';

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'card' => new class implements Cardable {
                /**
                 * @return string
                 */
                public function title(): string
                {
                    return 'Title of a longer featured blog post';
                }

                /**
                 * @return string
                 */
                public function description(): string
                {
                    return 'This is a wider card with supporting text below as a natural lead-in to additional content.
                            This content is a little bit longer. This is a wider card with supporting text below as
                            a natural lead-in to additional content. This content is a little bit longer.
                            This is a wider card with supporting text below as a natural lead-in to additional content.
                            This content is a little bit longer.';
                }

                /**
                 * @return string
                 */
                public function image(): ?string
                {
                    return 'https://picsum.photos/600/300';
                }

                /**
                 * @return mixed
                 */
                public function color(): ?Color
                {
                    return Color::INFO();
                }

                /**
                 * {@inheritdoc}
                 */
                public function status(): ?Color
                {
                    return Color::INFO();
                }
            },
            'cardCompendium' => new class implements Cardable {
                /**
                 * @return string
                 */
                public function title(): string
                {
                    return 'Kenmore 94149 Electric Range';
                }

                /**
                 * @return string
                 */
                public function description(): string
                {
                    return new Compendium([
                        'Type'                               => 'electric stove',
                        'Model'                              => 'dream 251CH',
                        'Main color'                         => 'white',
                        'Complementary color'                => 'none',
                        'Color declared by the manufacturer' => 'white',
                    ]);
                }

                /**
                 * @return string
                 */
                public function image(): ?string
                {
                    return null;
                }

                /**
                 * @return mixed
                 */
                public function color(): ?Color
                {
                    return Color::SUCCESS();
                }

                /**
                 * {@inheritdoc}
                 */
                public function status(): ?Color
                {
                    return Color::INFO();
                }
            },
            'cardPersona'    => new class implements Cardable {
                /**
                 * @return string
                 */
                public function title(): string
                {
                    return 'Prepare for presentation';
                }

                /**
                 * @return string
                 */
                public function description(): string
                {
                    return
                        '<p>This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>'.
                        new Facepile(User::limit(4)->get()->map->presenter());
                }

                /**
                 * @return string
                 */
                public function image(): ?string
                {
                    return null;
                }

                /**
                 * @return mixed
                 */
                public function color(): ?Color
                {
                    return Color::DANGER();
                }

                /**
                 * {@inheritdoc}
                 */
                public function status(): ?Color
                {
                    return Color::INFO();
                }
            },
        ];
    }

    /**
     * Button commands.
     *
     * @return Action[]
     */
    public function commandBar(): array
    {
        return [];
    }

    /**
     * Views.
     *
     * @throws \Throwable
     *
     * @return array
     */
    public function layout(): array
    {
        return [
            new Card('card', [
                Button::make('Example Button')
                    ->method('showToast')
                    ->icon('bag'),
                Button::make('Example Button')
                    ->method('showToast')
                    ->icon('bag'),
            ]),

            Layout::columns([
                new Card('cardPersona'),
                new Card('cardPersona', [
                    Button::make('Example Button')
                        ->method('showToast')
                        ->icon('bag'),

                    Button::make('Example Button')
                        ->method('showToast')
                        ->icon('bag'),
                ]),
            ]),

            new Card('cardCompendium'),
        ];
    }

    /**
     * @param Request $request
     */
    public function showToast(Request $request)
    {
        Toast::warning($request->get('toast', 'Hello, world! This is a toast message.'));
    }
}
