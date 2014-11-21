<?php namespace App;

use App\Contracts\Inspiring as InspiringContract;
use Illuminate\Support\Collection;

class Inspiring implements InspiringContract {

	/**
	 * Get an inspiring quote.
	 *
	 * Taylor & Dayle made this commit from Jungfraujoch. (11,333 ft.)
	 *
	 * @return string
	 */
	public function quote()
	{
		return Collection::make([

			'When there is no desire, all things are at peace. - Laozi',
			'Simplicity is the ultimate sophistication. - Leonardo da Vinci',
			'Simplicity is the essence of happiness. - Cedric Bledsoe',
			'Smile, breathe, and go slowly. - Thich Nhat Hanh',
			'Simplicity is an acquired taste. - Katharine Gerould',

		])->random();
	}

}
