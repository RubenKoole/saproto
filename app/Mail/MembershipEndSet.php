<?php

namespace Proto\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Proto\Models\User;

class MembershipEndSet extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('secretary@proto.utwente.nl', config('proto.secretary').' (Secretary)')
            ->subject('An end date is set for your membership of Proto.')
            ->view('emails.membershipenddate');
    }
}
