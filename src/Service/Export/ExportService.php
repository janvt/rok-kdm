<?php


namespace App\Service\Export;


use App\Entity\EquipmentInventory;
use App\Entity\Governor;
use App\Repository\EquipmentLoadoutRepository;
use App\Repository\GovernorRepository;
use App\Service\Governor\CommanderNames;
use App\Service\Governor\GovernorDetailsService;

class ExportService
{
    private $govDetailsService;
    private $govRepo;
    private $equipmentLoadoutRepo;
    private $siteTitle;

    public function __construct(
        GovernorDetailsService $govDetailsService,
        GovernorRepository $govRepo,
        EquipmentLoadoutRepository $equipmentLoadoutRepo,
        string $siteTitle
    ) {
        $this->govDetailsService = $govDetailsService;
        $this->govRepo = $govRepo;
        $this->equipmentLoadoutRepo = $equipmentLoadoutRepo;

        $this->siteTitle = $siteTitle;
    }

    public function streamGovDataExport(ExportFilter $filter): \Generator
    {
        foreach ($this->govRepo->getGovIterator($filter) as $result) {
            /** @var Governor $gov */
            $gov = $result[0];

            $details = $this->govDetailsService->createGovernorDetails(
                $gov,
                false,
                null,
                $filter->getSnapshot()
            );

            yield [
                'gov id' => $gov->getGovernorId(),
                'name' => $gov->getName(),
                'alliance' => $gov->getAlliance() ? $gov->getAlliance()->getTag() : '',
                'status' => $gov->getStatus(),
                'power' => $details->power,
                'highest power' => $details->highestPower,
                'kills' => $details->kills,
                't1 kills' => $details->t1kills,
                't2 kills' => $details->t2kills,
                't3 kills' => $details->t3kills,
                't4 kills' => $details->t4kills,
                't5 kills' => $details->t5kills,
                'deads' => $details->deads,
                'rss gathered' => $details->rssGathered,
                'rss assistance' => $details->rssAssistance,
                'helps' => $details->helps,
            ];
        }
    }

    public function streamCommanderExport(ExportFilter $filter): \Generator
    {
        foreach ($this->govRepo->getGovIterator($filter) as $result) {
            /** @var Governor $gov */
            $gov = $result[0];

            foreach ($gov->getCommanders() as $commander) {
                yield [
                    'gov id' => $gov->getGovernorId(),
                    'name' => $gov->getName(),
                    'alliance' => $gov->getAlliance() ? $gov->getAlliance()->getTag() : '',
                    'commander uid' => $commander->getUid(),
                    'commander name' => CommanderNames::ALL[$commander->getUid()],
                    'skills' => $commander->getSkills(),
                    'level' => $commander->getLevel(),
                ];
            }
        }
    }

    public function streamEquipmentExport(ExportFilter $filter): \Generator
    {
        foreach ($this->govRepo->getGovIterator($filter) as $result) {
            /** @var Governor $gov */
            $gov = $result[0];

            foreach ($this->equipmentLoadoutRepo->findBy(['governor' => $gov]) as $equipmentLoadout) {
                $loadoutData = [
                    'gov id' => $gov->getGovernorId(),
                    'name' => $gov->getName(),
                    'alliance' => $gov->getAlliance() ? $gov->getAlliance()->getTag() : '',
                    'set' => $equipmentLoadout->getName()
                ];

                foreach (EquipmentInventory::SLOTS as $slot) {
                    /** @var EquipmentInventory|null $currentSlot */
                    $currentSlot = $equipmentLoadout->{'getSlot' . $slot}();
                    $slotName = \strtolower($slot);

                    $loadoutData[$slotName . ' uid'] = $currentSlot ? $currentSlot->getUid() : null;
                    $loadoutData[$slotName . ' name'] = $currentSlot ? $currentSlot->getName() : null;
                    $loadoutData[$slotName . ' special'] = $equipmentLoadout->{'getSlot' . $slot . 'Special'}();
                }

                yield $loadoutData;
            }
        }
    }

    public function getFileName(ExportFilter $filter, $prefix = null): string
    {
        $parts = [
            $this->slugify($this->siteTitle),
        ];

        if ($prefix) {
            $parts[] = $prefix;
        }

        if ($filter->getAlliance()) {
            $parts[] = $filter->getAlliance()->getTag();
        }

        if ($filter->getSnapshot()) {
            $parts[] = $filter->getSnapshot()->getUid();
        }

        if ($filter->getGovStatus()) {
            $parts[] = $filter->getGovStatus();
        }

        $parts[] = date_format(new \DateTime, 'YmdHis');

        return implode('_', $parts);
    }

    private function slugify($text): string
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '_', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~_+~', '_', $text);
        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'roks_gg';
        }

        return $text;
    }
}