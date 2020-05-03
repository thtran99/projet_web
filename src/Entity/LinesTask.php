<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class LinesTask
{
  protected $lines;

  public function __construct()
  {
    $this->lines = new ArrayCollection();
  }

  public function getLines()
  {
    return $this->lines;
  }
}
