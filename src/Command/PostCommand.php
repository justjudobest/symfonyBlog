<?php

namespace App\Command;

use App\Controller\PostController;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\postService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PostCommand extends Command
{
    protected static $defaultName = 'importPost';
    private $postService;
    private $postRepository;
    private $categoryRepository;

    /**
     * PostCommand constructor.
     * @param PostController $postController
     * @param PostRepository $postRepository
     */
    public function __construct(postService $postService, PostRepository $postRepository,CategoryRepository $categoryRepository )
    {
        parent::__construct();
        $this->postService = $postService;
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
    }

    protected function configure()
    {
//        $this
//            ->setDescription('Add new admin')
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->setHelp('adds a new admin to the table');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
//        $arg1 = $input->getArgument('arg1');


//        if ($arg1) {
//            $io->note(sprintf('You passed an argument: %s', $arg1));
//        }

        if ($io->confirm('Do you want to import a file?')) {
            $this->postService->import($this->categoryRepository, $this->postRepository);
            $io->success('Posts imported!');
        }
        else {
            $io->error('Posts are not imported!');
        }

        return Command::SUCCESS;
    }

}
