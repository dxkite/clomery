<?php


namespace clomery\upload\provider;


use clomery\content\controller\CategoryController;
use clomery\content\controller\FileController;
use clomery\content\controller\TagController;
use clomery\content\parser\Content;
use clomery\main\controller\ArticleController;
use clomery\main\table\ArticleTable;
use clomery\main\table\AttachmentRelationTable;
use clomery\main\table\AttachmentTable;
use clomery\main\table\CategoryTable;
use clomery\main\table\TagRelationTable;
use clomery\main\table\TagTable;
use support\openmethod\parameter\File;
use support\upload\provider\BlockUploadProvider;
use support\upload\UploadUtil;
use support\visitor\provider\UserSessionAwareProvider;

class UploadProvider extends UserSessionAwareProvider
{
    /**
     * @param string $title
     * @param string $slug
     * @param string $description
     * @param string $content
     * @param array|null $category
     * @param array|null $tag
     * @param int $create
     * @param int $status
     * @return array|null
     * @throws \suda\database\exception\SQLException
     */
    public function save(string $title, string $slug, string $description, string $content, ?array $category, ?array $tag, int $create, int $status) {
        $articleController = new ArticleController(new ArticleTable(), new CategoryTable(), new TagTable(), new TagRelationTable());
        $categoryId = 0;
        $categoryController = $articleController->getCategoryController();
        if ($category !== null) {
            $categoryId = $categoryController->createWithOrderNameArray($category);
        }

        $data = [
            'title' => $title,
            'slug' => $slug,
            'description' => new Content($description, Content::MD),
            'content' => new Content($content, Content::MD),
            'category' => $categoryId,
            'create_time' => $create,
            'status' => $status,
        ];

        $save = $articleController->save($data);

        if ($save !== null && is_array($tag)) {
            if ($categoryId > 0) {
                $categoryController->updateCountItem($categoryId, 1);
            }
            $tagController = $articleController->getTagController();
            $tagArray = $tagController->createWithNameArray($tag);
            $tagController->linkTag($tagArray, $save['id']);
        }
        return $save;
    }

    /**
     * @param string $article
     * @param string $name
     * @param File $file
     * @return string
     * @throws \suda\database\exception\SQLException
     * @throws \ReflectionException
     */
    public function saveFile(string $article, string $name, File $file) {
        $controller = new FileController(new AttachmentTable(), new AttachmentRelationTable());
        $provider = new BlockUploadProvider();
        $provider->loadFromContext($this->context);
        $hash = UploadUtil::hash($file->getPathname());
        $uri = $provider->upload($file);
        $controller->saveFile($file, $name, $uri, $hash,  $article);
        return $uri;
    }
}