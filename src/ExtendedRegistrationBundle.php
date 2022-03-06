<?php

/*
 * This file is part of ExtendedRegistrationBundle.
 *
 * @package   ExtendedRegistrationBundle
 * @author    Marcel Mathias Nolte
 * @copyright Marcel Mathias Nolte 2015-2020
 * @website	  https://github.com/marcel-mathias-nolte
 * @license   LGPL-3.0-or-later
 */

namespace MarcelMathiasNolte\ExtendedRegistrationBundle;

use MarcelMathiasNolte\ExtendedRegistrationBundle\DependencyInjection\ExtendedRegistrationBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ExtendedRegistrationBundle extends Bundle
{

    public function getContainerExtension(): ExtendedRegistrationBundleExtension
    {
        return new ExtendedRegistrationBundleExtension();
    }
}
