<?php

namespace Gashey\BotmanUssdDriver\Lib;

class UssdResponse
{

    /**
     * RESPONSE TYPES:
     */
    /**
     * indicates that the application is ending the USSD session.
     */
    const RELEASE = '17';

    /**
     * indicates a response in an already existing USSD session.
     */
    const RESPONSE = '2';
}
