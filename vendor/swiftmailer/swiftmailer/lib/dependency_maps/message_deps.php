<?php

Swift_DependencyContainer::getInstance()
    ->register('message.message')
    ->asNewInstanceOf('Swift_Message')

    ->register('message.mimepart')
    ->asNewInstanceOf('Swift_MimePart')
;
