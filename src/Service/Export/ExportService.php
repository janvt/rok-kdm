<?php


namespace App\Service\Export;


use App\Entity\Alliance;
use App\Entity\Governor;
use App\Exception\ExportException;
use App\Repository\GovernorRepository;
use App\Service\Governor\GovernorDetailsService;

class ExportService
{
    private $govDetailsService;
    private $govRepo;
    private $siteTitle;

    public function __construct(
        GovernorDetailsService $govDetailsService,
        GovernorRepository $govRepo,
        string $siteTitle
    ) {
        $this->govDetailsService = $govDetailsService;
        $this->govRepo = $govRepo;
        $this->siteTitle = $siteTitle;
    }

    /**
     * @param ExportFilter $filter
     * @return \Generator
     */
    public function streamFullExport(ExportFilter $filter): \Generator
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

    public function getFileName(ExportFilter $filter): string
    {
        $parts = [
            $this->slugify($this->siteTitle),
        ];

        if ($filter->getAlliance()) {
            $parts[] = $filter->getAlliance()->getTag();
        }

        if ($filter->getSnapshot()) {
            $parts[] = $filter->getSnapshot()->getUid();
        }

        $parts[] = date_format(new \DateTime, 'YmdHis');

        return implode('_', $parts);
    }

    private function slugify($text): string
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // trim
        $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'roksgg';
        }

        return $text;
    }
}