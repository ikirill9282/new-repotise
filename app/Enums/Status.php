<?php


namespace App\Enums;


enum Status
{
  const ACTIVE = 1;
  const DRAFT = 2;
  const PENDING = 3;
  const REVISION = 4;
  const REJECT = 5;
  const SCHEDULED = 6;
  const DELETED = 7;
}