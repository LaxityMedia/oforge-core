<?php

namespace Oforge\Engine\Modules\Cronjob\Cronjobs;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Annotation\ORM\Discriminator\DiscriminatorEntry;
use Oforge\Engine\Modules\Cronjob\Models\CommandCronjob;

/**
 * Class LogCleanupCronjob
 *
 * @ORM\Entity
 * @DiscriminatorEntry()
 * @package Oforge\Engine\Modules\Cronjob\Cronjobs
 */
class LogCleanupCronjob extends CommandCronjob {

    public function __construct() {
        parent::__construct();
        $this->fromArray([
            'id'                => 'oforge:cleanup:logs',
            'title'             => 'Cleanup log files',
            'executionInterval' => 7 * 24 * 60 * 60,
            'command'           => 'oforge:cleanup:logs',
        ]);
    }

}
