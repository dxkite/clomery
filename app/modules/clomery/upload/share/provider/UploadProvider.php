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
use clomery\upload\exception\UploadException;
use support\openmethod\parameter\File;
use support\upload\provider\BlockUploadProvider;
use support\upload\UploadUtil;
use support\visitor\provider\UserSessionAwareProvider;

class UploadProvider extends UserSessionAwareProvider
{
    /**
     * @var string
     */
    protected $group = 'clomery';

    /**
     * @param string $title
     * @param string $slug
     * @param string $description
     * @param string $content
     * @param array|null $category
     * @param array|null $tag
     * @param int $create
     * @param int $status
     * @param array $attribute
     * @return array|null
     * @throws \suda\database\exception\SQLException
     */
    public function save(string $title, string $slug, string $description, string $content, ?array $category, ?array $tag, int $create, int $status, array $attribute = [])
    {

        if ($this->visitor->isGuest()) {
            throw new UploadException('error user', UploadException::ERR_USER_ID);
        }

        $articleController = new ArticleController(new ArticleTable(), new CategoryTable(), new TagTable(), new TagRelationTable());
        $categoryId = 0;
        $oldCid = 0;
        $categoryController = $articleController->getCategoryController();

        if ($category !== null) {
            $categoryId = $categoryController->createWithOrderNameArray($category);
        }


        $data = [
            'user' => $this->visitor->getId(),
            'title' => $title,
            'slug' => $slug,
            'description' => new Content($description, Content::MD),
            'content' => new Content($content, Content::MD),
            'category' => $categoryId,
            'create_time' => $create,
            'status' => $status,
        ];

        $want = $articleController->getArticle($slug, ['id', 'category', 'content_hash', 'modify_time']);
        if ($want !== null) {
            $oldCid = $want['category'];
            $data['id'] = $want['id'];
            $hash = strtolower(md5($content));
            $data['content_hash'] = $hash;
            $data['modify_time'] = $want['modify_time'];
            if (strcmp($hash, strtolower($want['content_hash'])) != 0) {
                $data['modify_time'] = $data['modify_time'] ?? time();
            }
        }

        $save = $articleController->save($data);

        if ($save !== null) {
            if (is_array($tag)) {
                $tagController = $articleController->getTagController();
                $tagArray = $tagController->createWithNameArray($tag);
                $tagController->remove($save['id']);
                $tagController->linkTag($tagArray, $save['id']);
            }
            $rewriteCategory = $attribute['rewrite_category_count'] ?? false;
            if ($oldCid != $categoryId) {
                if ($categoryId > 0) {
                    $rewriteCategory || $categoryController->pushCountItem($categoryId, 1);
                    $rewriteCategory && $categoryController->writeCount($categoryId, $articleController->getCategoryCount($categoryId));
                }
                if ($oldCid > 0) {
                    $rewriteCategory || $categoryController->pushCountItem($oldCid, -1);
                    $rewriteCategory && $categoryController->writeCount($oldCid, $articleController->getCategoryCount($oldCid));
                }
            }
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
    public function saveFile(string $article, string $name, File $file)
    {
        if ($this->visitor->isGuest()) {
            throw new UploadException('error user', UploadException::ERR_USER_ID);
        }
        $controller = new FileController(new AttachmentTable(), new AttachmentRelationTable());
        $provider = new BlockUploadProvider();
        $provider->loadFromContext($this->context);
        $hash = UploadUtil::hash($file->getPathname());
        $uri = $provider->upload($file);
        $controller->saveFile($this->visitor->getId(), $file, $name, $uri, $hash, $article);
        return $uri;
    }
}