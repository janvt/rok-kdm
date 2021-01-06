<?php


namespace App;


use App\Entity\Governor;
use App\Exception\APIException;
use App\Repository\GovernorRepository;

class GovernorService
{
    private $govRepo;

    public function __construct(
        GovernorRepository $govRepo
    )
    {
        $this->govRepo = $govRepo;
    }

    /**
     * @param object $data
     * @return Governor
     * @throws APIException
     */
    public function createGovernor(object $data): Governor
    {
        $govId = $data->id;
        if (!$govId) {
            throw new APIException('Missing gov id!');
        }

        $existingGov = $this->govRepo->findBy(['governor_id' => $govId]);
        if ($existingGov) {
            throw new APIException('Gov id already exists: ' . $govId);
        }

        $gov = new Governor();
        $gov->setGovernorId($govId);
        $gov->setName($data->name);

        return $this->govRepo->save($gov);
    }
}