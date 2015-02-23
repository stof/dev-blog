<?php

namespace Symbid\DevBlog\AuthorsBundle\Twig;

class AuthorExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    protected $authors;

    /**
     * Constructor
     *
     * @param $authors
     */
    public function __construct(array $authors)
    {
        $this->authors = $authors;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'symbid_authors_extension';
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('get_authors_data', [$this, 'getAuthorsData']),
            new \Twig_SimpleFunction('get_author_data', [$this, 'getAuthorData'])
        ];
    }

    /**
     * @return array
     */
    public function getAuthorsData()
    {
        return $this->authors;
    }

    public function getAuthorData($handle)
    {
        if (! array_key_exists($handle, $this->authors)) {
            return false;
        }

        return $this->authors[$handle];
    }
}
