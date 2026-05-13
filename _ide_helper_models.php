<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BillingTransaction query()
 */
	class BillingTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Domain|null $domain
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord query()
 */
	class DnsRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DnsRecord> $dnsRecords
 * @property-read int|null $dns_records_count
 * @property-read \App\Models\HostingAccount|null $hostingAccount
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Domain query()
 */
	class Domain extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\Domain|null $domain
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HostingAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HostingAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HostingAccount query()
 */
	class HostingAccount extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BillingTransaction> $billingTransactions
 * @property-read int|null $billing_transactions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Domain> $domains
 * @property-read int|null $domains_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HostingAccount> $hostingAccounts
 * @property-read int|null $hosting_accounts_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 */
	class User extends \Eloquent {}
}

