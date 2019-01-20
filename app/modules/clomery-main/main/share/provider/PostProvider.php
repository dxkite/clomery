<?php
namespace dxkite\clomery\main\provider;

use dxkite\support\file\File;
use dxkite\support\util\Pinyin;
use dxkite\support\view\PageData;
use dxkite\content\parser\Content;
use dxkite\support\file\UploadFile;
use dxkite\article\table\ArticleTable;
use dxkite\article\provider\ArticleProvider;
use dxkite\article\controller\ArticleCategoryController;
use dxkite\article\controller\ArticleAttachmentController;

class PostProvider
{
    /**
     * 文章控制器
     *
     * @var ArticleProvider
     */
    protected $provider;
    /**
     * 分类控制器
     *
     * @var ArticleCategoryController
     */
    protected $categoryController;

    /**
     * 附件控制器
     *
     * @var ArticleAttachmentController
     */
    protected $attachmentController;

    public function __construct()
    {
        // 临时权限验证
        if (request()->getHeader('Clomery-Token')  === \trim(file_get_contents(DATA_DIR.'/token'))) {
            // 登陆ID为1的账号
            \visitor()->signin(1);
        }
        $this->provider = new ArticleProvider;
        $this->categoryController = new ArticleCategoryController;
        $this->attachmentController= new ArticleAttachmentController;
    }

    /**
     * 写入文章
     *
     * @acl article.write:article
     * @param integer|null $id 文章ID/修改则填入
     * @param string $title 文章标题
     * @param string|null $slug 文章唯一标识
     * @param string $category 文章分类名称
     * @param integer $cover 文章封面
     * @param array|null $tags
     * @param Content $excerpt 文章摘要
     * @param Content $content 文章内容
     * @param integer|null $create
     * @param integer|null $modify 文章修改时间
     * @param integer $status 文章状态
     * @return integer 文章id
     */
    public function save(
        ?int $id =null,
        string $title,
        ?string $slug=null,
        string $category,
        int $cover= 0,
        ?array $tags= null,
        Content $excerpt,
        Content $content,
        ?int $create=null,
        ?int $modify=null,
        int $status=ArticleTable::STATUS_DRAFT
    ) :int {
        $canCreateCategory = visitor()->hasPermission('article.write:category');
        if ($categoryInfo = $this->categoryController->getByName($category)) {
            $categoryId = $categoryInfo['id'];
        } elseif ($canCreateCategory && strlen($category) > 0) {
            $categoryId = $this->categoryController->add(\visitor()->getId(), $category, Pinyin::getAll($category));
        } else {
            $categoryId = 0;
        }
        return $this->provider->save($id, $title, $slug, $categoryId, $cover, $tags, $excerpt, $content, $create, $modify, $status);
    }

    public function delete(int $article):int
    {
        return $this->provider->delete($article);
    }
    
    public function saveImage(int $article, ?string $name=null, File $image):?UploadFile
    {
        return $this->attachmentController->addImage($article, $name ?? $image->getName(), $image);
    }

    public function saveAttachment(int $article, string $name, File $attachment):?UploadFile
    {
        return $this->attachmentController->addAttachment($article, $name, $attachment);
    }
}
