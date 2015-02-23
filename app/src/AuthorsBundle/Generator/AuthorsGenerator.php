<?php

namespace Symbid\DevBlog\AuthorsBundle\Generator;

use Sculpin\Core\DataProvider\DataProviderManager;
use Sculpin\Core\Generator\GeneratorInterface;
use Sculpin\Core\Source\SourceInterface;

class AuthorsGenerator implements GeneratorInterface
{
    /**
     * @var array
     */
    protected $authors;

    /**
     * @var DataProviderManager
     */
    protected $dataProviderManager;

    /**
     * Constructor
     *
     * @param DataProviderManager $dataProviderManager
     * @param array $authors
     */
    public function __construct(DataProviderManager $dataProviderManager, array $authors)
    {
        $this->authors = $authors;
        $this->dataProviderManager = $dataProviderManager;
    }

    /**
     * Generate generated sources from generator source
     *
     * @param SourceInterface $source Source (generator)
     *
     * @return SourceInterface[]
     */
    public function generate(SourceInterface $source)
    {
        $generatedSources = [];

        foreach ($this->authors as $handle => $author) {

            //get posts
            $posts = [];
            foreach ($this->dataProviderManager->dataProvider('posts')->provideData() as $post) {
                if (in_array($handle, $post['authors'])) {
                    $posts[] = $post;
                }
            }

            $generatedSource = $source->duplicate(
                $source->sourceId() . ':author=' . $handle);

            $permalink = $source->data()->get('permalink') ?: $source->relativePathname();
            $permalink = sprintf('%s/%s/index.html', dirname($permalink), $handle);

            $generatedSource->data()->set('permalink', $permalink);
            $generatedSource->data()->set('author', $author);
            $generatedSource->data()->set('author_handle', $handle);
            $generatedSource->data()->set('author_posts', $posts);

            $generatedSources[] = $generatedSource;
        }

        return $generatedSources;
    }

}
