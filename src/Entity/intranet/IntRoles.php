<?php

namespace App\Entity\intranet;

use App\Repository\RolesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="int_roles")
 * @ORM\Entity(repositoryClass=RolesRepository::class)
 */
class IntRoles
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nombre_role;

    /**
     * @ORM\Column(type="string", length=250)
     */
    public $descripcion_es;
    
    /**
     * @ORM\Column(type="string", length=250)
     */
    public $descripcion_en;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreRole(): ?string
    {
        return $this->nombre_role;
    }

    public function setNombreRole(string $nombre_role): self
    {
        $this->nombre_role = $nombre_role;

        return $this;
    }

    public function getDescripcion($lang): ?string
    {
        if ($lang=="es") return $this->descripcion_es;
        if ($lang=="en") return $this->descripcion_en;
        
        return $this->descripcion_es;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }
}
