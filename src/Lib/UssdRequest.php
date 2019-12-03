<?php

namespace Gashey\BotmanUssdDriver\Lib;

class UssdRequest
{

    /**
     * REQUEST TYPES:
     */

    /**
     * indicates the first message in a USSD Session
     */
    const INITIATION = '1';

    /**
     * indicates a follow up in an already existing USSD session.
     */
    const RESPONSE = '18';

    /**
     * indicates that the subscriber is ending the USSD session.
     */
    const RELEASE = '30';

    /**
     * indicates that the USSD session has timed out.
     */
    const TIMEOUT = 'Timeout';

    /**
     * indicates that the user data should not be passed onto Hubtel (Safaricom Only).
     */
    const HIJACKSESSION = 'HijackSession';
}
