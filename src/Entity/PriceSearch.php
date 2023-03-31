<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
class PriceSearch
{
    
#[ORM\Column(type: Types::DECIMAL)]
 private ?string $minPrice =null;
 #[ORM\Column(type: Types::DECIMAL)]
 private ?string $maxPrice =null;

 public function getMinPrice(): ?string
 {
 return $this->minPrice;
 }
 public function setMinPrice(int $minPrice): self
 { $this->minPrice = $minPrice;
 return $this;
 }
 public function getMaxPrice(): ?string
 {
 return $this->maxPrice;
 }
 public function setMaxPrice(int $maxPrice): self
 {
 $this->maxPrice = $maxPrice;
 return $this;
 }
}
?>