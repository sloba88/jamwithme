<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jam\CoreBundle\Model;

use SplFileInfo;
use DateTime;

interface ImageInterface extends TimestampableInterface
{
    public function getId();
    public function hasFile();
    public function getFile();
    public function setFile(SplFileInfo $file);
    public function getPath();
    public function setPath($path);
}
