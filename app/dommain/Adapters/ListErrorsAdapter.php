<?php
namespace App\Dommain;

use Illuminate\Support\MessageBag;

class ListErrorAdapter implements Adapter {
    private $dataSource;
    private $converted = [];

    /**
     * @param MessageBag $dataSource
     * @return void
     */
    public function sourcer($dataSource) {
        $this->converted = [];
        $this->dataSource = $dataSource;
    }

    /**
     * @return array
     */
    public function convert() {
        return $this->getSourceConverted();
    }

    private function getSourceConverted()
    {
        if (count($this->converted)) return $this->converted;

        $data = [];

        foreach ($this->dataSource as $row) {
            foreach ($row as $value) {
                $data[] = $value;
            }
        }

        return $this->converted = $data;
    }
}
