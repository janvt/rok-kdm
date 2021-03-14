<?php


namespace App\Service\Import;


interface ImportReader
{
   public function getHeader(): array;
   public function readLines(): \Iterator;
}