<?php
namespace App\Dommain;

/**
 * Convert all object in a new one.
 */
interface Adapter {
    /**
     * Source of the conversation.
     * @return void
     */
    public function sourcer($dataSource);
    /**
     * Instance was converted.
     * @return \Object
     */
    public function convert();
}
