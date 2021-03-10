<?php


namespace App\Service\Export;


use App\Entity\Alliance;
use App\Entity\Snapshot;
use Symfony\Component\Form\FormInterface;

class ExportFilter
{
    /** @var Alliance|null */
    private $alliance;
    /** @var Snapshot|null */
    private $snapshot;
    /** @var string|null */
    private $govStatus;

    public function __construct(FormInterface $form)
    {
        $this->alliance = $form->get('alliance')->getData();
        $this->snapshot = $form->get('snapshot')->getData();
        $this->govStatus = $form->get('govStatus')->getData();
    }

    /**
     * @return Alliance|null
     */
    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    /**
     * @return Snapshot|null
     */
    public function getSnapshot(): ?Snapshot
    {
        return $this->snapshot;
    }

    /**
     * @return string|null
     */
    public function getGovStatus(): ?string
    {
        return $this->govStatus;
    }
}