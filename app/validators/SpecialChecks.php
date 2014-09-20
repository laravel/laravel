<?php

class SpecialChecks
{
    public function checkSomething($thatThing)
    {
        return __METHOD__;
    }

    // This method exists solely to show that Laravel is failing
    // to honour the extension declaration.
    public function validateSomething($thatThing)
    {
        return __METHOD__;
    }
}
