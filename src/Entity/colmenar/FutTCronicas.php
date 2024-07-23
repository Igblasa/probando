<?php

namespace App\Entity\colmenar;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * FutTCronicas
 *
 * @ORM\Table(name="fut_t_cronicas", indexes={@ORM\Index(name="id_partido", columns={"id_partido"})})
 * @ORM\Entity
 */
class FutTCronicas
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_cronica", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCronica;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mvp", type="string", length=100, nullable=true)
     */
    private $mvp;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mvp_semana", type="string", length=100, nullable=true)
     */
    private $mvpSemana;

    /**
     * @var string|null
     *
     * @ORM\Column(name="texto_cronica", type="text", length=0, nullable=true)
     */
    private $textoCronica;
    
    /**
     * @var string|null
     *
     * @ORM\Column(name="texto_cronica_chatgpt", type="text", length=0, nullable=true)
     */
    private $texto_cronica_chatgpt;

    /**
     * @var string|null
     *
     * @ORM\Column(name="goleador1", type="string", length=100, nullable=true)
     */
    private $goleador1;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numero_goles_1", type="integer", nullable=true)
     */
    private $numeroGoles1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="goleador2", type="string", length=100, nullable=true)
     */
    private $goleador2;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numero_goles_2", type="integer", nullable=true)
     */
    private $numeroGoles2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="goleador3", type="string", length=100, nullable=true)
     */
    private $goleador3;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numero_goles_3", type="integer", nullable=true)
     */
    private $numeroGoles3;

    /**
     * @var string|null
     *
     * @ORM\Column(name="goleador4", type="string", length=100, nullable=true)
     */
    private $goleador4;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numero_goles_4", type="integer", nullable=true)
     */
    private $numeroGoles4;

    /**
     * @var string|null
     *
     * @ORM\Column(name="goleador5", type="string", length=100, nullable=true)
     */
    private $goleador5;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numero_goles_5", type="integer", nullable=true)
     */
    private $numeroGoles5;

    /**
     * @var int|null
     *
     * @ORM\Column(name="goleador6", type="string", length=100, nullable=true)
     */
    private $goleador6;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numero_goles_6", type="integer", nullable=true)
     */
    private $numeroGoles6;

    /**
     * @var string|null
     *
     * @ORM\Column(name="goleador7", type="string", length=100, nullable=true)
     */
    private $goleador7;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numero_goles_7", type="integer", nullable=true)
     */
    private $numeroGoles7;
    
    /**
     * @var string|null
     * @ORM\Column(name="publicado_en_redes", type="string", length=2, nullable=true)
     */
    private $publicadoEnRedes;
    
    /**
     * @var string|null
     * @ORM\Column(name="enviado_whatsapp", type="string", length=2, nullable=true)
     */
    private $enviadoWhatsapp;

    /**
     * @var \FutUEquiposRivales
     *
     * @ORM\ManyToOne(targetEntity="FutUEquiposRivales")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_partido", referencedColumnName="id_partido")
     * })
     */
    private $idPartido;

    public function getIdCronica(): ?int
    {
        return $this->idCronica;
    }

    public function getMvp(): ?string
    {
        return $this->mvp;
    }

    public function setMvp(?string $mvp): self
    {
        $this->mvp = $mvp;

        return $this;
    }

    public function getMvpSemana(): ?string
    {
        return $this->mvpSemana;
    }

    public function setMvpSemana(?string $mvpSemana): self
    {
        $this->mvpSemana = $mvpSemana;

        return $this;
    }

    public function getTextoCronica(): ?string
    {
        return $this->textoCronica;
    }

    public function setTextoCronica(?string $textoCronica): self

    {
        $this->textoCronica = $textoCronica;

        return $this;
    }
    
    public function getTextoCronicaChatgpt(): ?string
    {
        return $this->texto_cronica_chatgpt;
    }

    public function setTextoCronicaChatgpt(?string $textoCronicaChatgpt): self

    {
        $this->texto_cronica_chatgpt = $textoCronicaChatgpt;

        return $this;
    }

    public function getGoleador1(): ?string
    {
        return $this->goleador1;
    }

    public function setGoleador1(?string $goleador1): self
    {
        $this->goleador1 = $goleador1;

        return $this;
    }

    public function getNumeroGoles1(): ?int
    {
        return $this->numeroGoles1;
    }

    public function setNumeroGoles1(?int $numeroGoles1): self
    {
        $this->numeroGoles1 = $numeroGoles1;

        return $this;
    }

    public function getGoleador2(): ?string
    {
        return $this->goleador2;
    }

    public function setGoleador2(?string $goleador2): self
    {
        $this->goleador2 = $goleador2;

        return $this;
    }

    public function getNumeroGoles2(): ?int
    {
        return $this->numeroGoles2;
    }

    public function setNumeroGoles2(?int $numeroGoles2): self
    {
        $this->numeroGoles2 = $numeroGoles2;

        return $this;
    }

    public function getGoleador3(): ?string
    {
        return $this->goleador3;
    }

    public function setGoleador3(?string $goleador3): self
    {
        $this->goleador3 = $goleador3;

        return $this;
    }

    public function getNumeroGoles3(): ?int
    {
        return $this->numeroGoles3;
    }

    public function setNumeroGoles3(?int $numeroGoles3): self
    {
        $this->numeroGoles3 = $numeroGoles3;

        return $this;
    }

    public function getGoleador4(): ?string
    {
        return $this->goleador4;
    }

    public function setGoleador4(?string $goleador4): self
    {
        $this->goleador4 = $goleador4;

        return $this;
    }

    public function getNumeroGoles4(): ?int
    {
        return $this->numeroGoles4;
    }

    public function setNumeroGoles4(?int $numeroGoles4): self
    {
        $this->numeroGoles4 = $numeroGoles4;

        return $this;
    }

    public function getGoleador5(): ?string
    {
        return $this->goleador5;
    }

    public function setGoleador5(?string $goleador5): self
    {
        $this->goleador5 = $goleador5;

        return $this;
    }

    public function getNumeroGoles5(): ?int
    {
        return $this->numeroGoles5;
    }

    public function setNumeroGoles5(?int $numeroGoles5): self
    {
        $this->numeroGoles5 = $numeroGoles5;

        return $this;
    }

    public function getGoleador6(): ?string
    {
        return $this->goleador6;
    }

    public function setGoleador6(?string $goleador6): self
    {
        $this->goleador6 = $goleador6;

        return $this;
    }

    public function getNumeroGoles6(): ?int
    {
        return $this->numeroGoles6;
    }

    public function setNumeroGoles6(?int $numeroGoles6): self
    {
        $this->numeroGoles6 = $numeroGoles6;

        return $this;
    }

    public function getGoleador7(): ?string
    {
        return $this->goleador7;
    }

    public function setGoleador7(?string $goleador7): self
    {
        $this->goleador7 = $goleador7;

        return $this;
    }

    public function getNumeroGoles7(): ?int
    {
        return $this->numeroGoles7;
    }

    public function setNumeroGoles7(?int $numeroGoles7): self
    {
        $this->numeroGoles7 = $numeroGoles7;

        return $this;
    }

    public function getIdPartido(): ?FutUEquiposRivales
    {
        return $this->idPartido;
    }

    public function setIdPartido(?FutUEquiposRivales $idPartido): self
    {
        $this->idPartido = $idPartido;

        return $this;
    }
    
    public function getPublicadoEnRedes(): ?string
    {
        return $this->publicadoEnRedes;
    }

    public function setPublicadoEnRedes(?string $publicadoEnRedes): self
    {
        $this->publicadoEnRedes = $publicadoEnRedes;

        return $this;
    }

    public function getEnviadoWhatsapp(): ?string
    {
        return $this->enviadoWhatsapp;
    }

    public function setEnviadoWhatsapp(?string $enviadoWhatsapp): self
    {
        $this->enviadoWhatsapp = $enviadoWhatsapp;

        return $this;
    }
}
