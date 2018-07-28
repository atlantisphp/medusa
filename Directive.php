<?php

namespace AtlantisPHP\Medusa;

class Directive
{
  /**
   * Override directive
   *
   * @return
   */
  public function directive()
  {
    if (isset($this->directive)) {
      return $this->directive;
    }

    return false;
  }

  /**
   * Directive extends
   *
   * @return
   */
  public function extends()
  {
    if (isset($this->extends)) {
      return $this->extends;
    }

    return false;
  }

  /**
   * Directive uses
   *
   * @return
   */
  public function uses()
  {
    if (isset($this->uses)) {
      return $this->uses;
    }

    return false;
  }

  /**
   * Directive name
   *
   * @return
   */
  public function name()
  {
    if (isset($this->name)) {
      return $this->name;
    }

    return false;
  }


}