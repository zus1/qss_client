<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\User;
use App\Service\Cache;
use App\Service\Qss;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAuthorCommand extends Command
{
    protected static $defaultName = 'app:create-author';
    private $qss;
    private $validator;
    private $authorApi;

    public function __construct(Qss $qss, ValidatorInterface $validator, \App\Api\Author $authorApi, string $name = null) {
        $this->qss = $qss;
        $this->validator = $validator;
        $this->authorApi = $authorApi;
        parent::__construct($name);
    }

    protected function configure() {
        $this->addOption('memcached', null, InputOption::VALUE_OPTIONAL, 'Is memcached living and breathing?');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if($input->getOption('memcached')) {
            if(extension_loaded("memcached")) {
                $io->success('You got it"');
                return Command::SUCCESS;
            } else {
                $io->error("Swing and miss! Install memcached php extension to swing a home run!");
                return Command::FAILURE;
            }
        }

        $io->title('Creating a new author for qss');

        $io->section("Logged user check");

        $io->text('Lets check are you logged in first.');
        $cachedUser = $this->geCachedUserWithCheck($io);

        $io->section("Create Author");

        $authorEntity = new Author();
        $name = $this->askWithValidation($io, 'What is Authors first name?', $authorEntity, "name", null, "Name is Required");
        $lName = $this->askWithValidation($io, 'What is Authors last name?', $authorEntity, "lName", null, "Last name is Required.");
        $gender = $io->choice('Please specify Authors gender', ['male', 'female']);
        $placeOfBirth = $this->askWithValidation($io, 'Where is Author born?', $authorEntity, "placeOfBirth", null, "Place of birth is required is Required.");
        $birthday = $this->askWithValidation($io, 'When is Author born(Format Y-m-d)?', $authorEntity, "birthDay", null, "Birthday is Required.");
        $biography = $this->askWithValidation($io, 'Say something about this Author', $authorEntity, "biography", "");

        $io->note(sprintf('Authors name is: %s', $name));
        $io->note(sprintf('Authors last name is: %s', $lName));
        $io->note(sprintf('Authors gender is: %s', $gender));
        $io->note(sprintf('Author was born in: %s', $placeOfBirth));
        $io->note(sprintf('Authors birthday is at: %s', $birthday));
        if($biography !== "") {
            $io->note(sprintf('Authors biography: %s', $biography));
        }

        $authorEntity->setName($name);
        $authorEntity->setLName($lName);
        $authorEntity->setGender($gender);
        $authorEntity->setPlaceOfBirth($placeOfBirth);
        $authorEntity->setBirthDay($birthday);
        $authorEntity->setBiography($biography);

        $this->authorApi->setTokenOverride($cachedUser->getToken());
        $this->qss->setCallClass($this->authorApi)->authorAdd($authorEntity);

        $io->success('You just created a living breathing author person, you are ruler of the universe!');

        return Command::SUCCESS;
    }

    /**
     * @param SymfonyStyle $io
     * @return User
     */
    private function geCachedUserWithCheck(SymfonyStyle $io) : User {
        $email = $this->askWithValidation($io, 'Enter you account email', new User(), "email", null, "Please enter email");
        $cachedUser = Cache::load()->get(Cache::USER_CACHE_KEY, array("email" => $email));
        if(empty($cachedUser)) {
            throw new \RuntimeException("Not logged in!");
        }

        return $cachedUser;
    }

    /**
     * @param SymfonyStyle $io
     * @param string $question
     * @param $entity
     * @param string $propertyForValidation
     * @param string|null $defaultAnswer
     * @param string|null $missingMessage
     * @return mixed
     */
    private function askWithValidation(SymfonyStyle $io, string $question, $entity, string $propertyForValidation, ?string $defaultAnswer="", ?string $missingMessage="") {
        return $io->ask($question, $defaultAnswer, function($answer) use($entity, $propertyForValidation, $missingMessage) {
            if(empty($answer) && $missingMessage !== "") {
                throw new \RuntimeException($missingMessage);
            }
            $this->makeValidation($this->validator, $entity, $propertyForValidation, $answer);

            return $answer;
        });
    }

    /**
     * @param ValidatorInterface $validator
     * @param $entity
     * @param string $name
     * @param $value
     */
    private function makeValidation(ValidatorInterface $validator, $entity, string $name, $value) : void {
        $failed = $validator->validatePropertyValue($entity, $name, $value);
        if($failed->count() > 0) {
            throw new \RuntimeException($failed->get(0)->getMessage());
        }
    }
}
