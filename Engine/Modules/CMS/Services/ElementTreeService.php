<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentParent;
use Oforge\Engine\Modules\CMS\Models\Content\Content;

class ElementTreeService extends AbstractDatabaseAccess
{
    private $entityManager;
    private $repository;
    
    public function __construct()
    {
        parent::__construct(["contentTypeGroup" => ContentTypeGroup::class, "contentType" => ContentType::class, "contentParent" => ContentParent::class, "content" => Content::class]);
    }
    
    /**
     * Returns all available content type group entities
     *
     * @return ContentTypeGroup[]|NULL
     */
    private function getContentTypeGroupEntities()
    {
        $contentTypeGroupEntityArray = $this->repository("contentTypeGroup")->findAll();
        
        if (isset($contentTypeGroupEntityArray))
        {
            return $contentTypeGroupEntityArray;
        }
        else
        {
            return NULL;
        }
    }
    
    /**
     * Returns all available content type entities
     *
     * @return ContentType[]|NULL
     */
    private function getContentTypeEntities()
    {
        $contentTypeEntityArray = $this->repository("contentType")->findAll();
        
        if (isset($contentTypeEntityArray))
        {
            return $contentTypeEntityArray;
        }
        else
        {
            return NULL;
        }
    }
    
    /**
     * Returns all available content parent entities
     *
     * @return ContentParent[]|NULL
     */
    private function getContentParentEntities()
    {
        $contentParentEntityArray = $this->repository("contentParent")->findAll();
        
        if (isset($contentParentEntityArray))
        {
            return $contentParentEntityArray;
        }
        else
        {
            return NULL;
        }
    }
    
    /**
     * Returns all available content entities
     *
     * @return Content[]|NULL
     */
    private function getContentEntities()
    {
        $contentEntityArray = $this->repository("content")->findAll();
        
        if (isset($contentEntityArray))
        {
            return $contentEntityArray;
        }
        else
        {
            return NULL;
        }
    }
    
    /**
     * Returns all found content type groups as an associative array
     *
     * @return array|NULL Array filled with available content type groups
     */
    public function getContentTypeGroupArray()
    {
        $contentTypeGroupEntities = $this->getContentTypeGroupEntities();
        
        if (!$contentTypeGroupEntities)
        {
            return NULL;
        }
        
        $contentTypeGroups = [];
        foreach($contentTypeGroupEntities as $contentTypeGroupEntity)
        {
            $contentTypeGroup = [];
            $contentTypeGroup["id"]          = $contentTypeGroupEntity->getId();
            $contentTypeGroup["name"]        = $contentTypeGroupEntity->getName();
            $contentTypeGroup["description"] = $contentTypeGroupEntity->getDescription();
            
            $contentTypeGroups[] = $contentTypeGroup;
        }
        
        return $contentTypeGroups;
    }
    
    /**
     * Returns all found content types as an associative array
     *
     * @return array|NULL Array filled with available content types
     */
    public function getContentTypeArray()
    {
        $contentTypeEntities = $this->getContentTypeEntities();
        
        if (!$contentTypeEntities)
        {
            return NULL;
        }
        
        $contentTypes = [];
        foreach($contentTypeEntities as $contentTypeEntity)
        {
            $contentType = [];
            $contentType["id"]          = $contentTypeEntity->getId();
            $contentType["parent"]      = $contentTypeEntity->getGroup()->getName();
            $contentType["name"]        = $contentTypeEntity->getName();
            $contentType["description"] = $contentTypeEntity->getDescription();
            
            $contentTypes[] = $contentType;
        }
        
        return $contentTypes;
    }
    
    /**
     * Returns all found content parents as an associative array
     *
     * @return array|NULL Array filled with available content parents
     */
    public function getContentParentArray()
    {
        $contentParentEntities = $this->getContentParentEntities();
        
        if (!$contentParentEntities)
        {
            return NULL;
        }
        
        $contentParents = [];
        foreach($contentParentEntities as $contentParentEntity)
        {
            $contentParent = [];
            $contentParent["id"]          = $contentParentEntity->getId();
            $contentParent["parent"]      = ($contentParentEntity->getParent() && $contentParentEntity->getParent()->getId()) ? "_parent#" . $contentParentEntity->getParent()->getId() : "#";
            $contentParent["description"] = $contentParentEntity->getDescription();
            
            $contentParents[] = $contentParent;
        }
        
        return $contentParents;
    }
    
    /**
     * Returns all found content elements as an associative array
     *
     * @return array|NULL Array filled with available content elements
     */
    public function getContentElementArray()
    {
        $contentEntities = $this->getContentEntities();
        
        if (!$contentEntities)
        {
            return NULL;
        }
        
        $contentElements = [];
        foreach($contentEntities as $contentEntity)
        {
            $contentElement = [];
            $contentElement["id"]     = $contentEntity->getId();
            $contentElement["type"]   = $contentEntity->getType()->getName();
            $contentElement["parent"] = $contentEntity->getParent() ? $contentEntity->getParent()->getId() : false;
            $contentElement["name"]   = $contentEntity->getName();
            
            $contentElements[] = $contentElement;
        }
        
        return $contentElements;
    }
    
    /**
     * Generate a jsTree configuration file with content element data included
     *
     * @return array|NULL jsTree configuration file as PHP array
     */
    public function generateJsTreeConfigJSON()
    {
        $contentTypeGroups = $this->getContentTypeGroupArray();
        $contentTypes      = $this->getContentTypeArray();
        $contentParents    = $this->getContentParentArray();
        $contentElements   = $this->getContentElementArray();
        
        if (!$contentTypeGroups || !$contentTypes || !$contentElements)
        {
            return NULL;
        }
        
        $jsTreeContentElementData = [];

        foreach ($contentTypeGroups as $contentTypeGroup)
        {
            $jsTreeContentElementData[] = [
                "id"     => $contentTypeGroup["name"],
                "icon"   => "jstree-folder",
                "parent" => "#",
                "text"   => $contentTypeGroup["description"]
            ];
        }
        
        foreach ($contentTypes as $contentType)
        {
            $jsTreeContentElementData[] = [
                "id"     => $contentType["name"],
                "icon"   => "jstree-folder",
                "parent" => $contentType["parent"],
                "text"   => $contentType["description"]
            ];
        }
        
        foreach ($contentParents as $contentParent)
        {
            $jsTreeContentElementData[] = [
                "id"     => "_parent#" . $contentParent["id"],
                "icon"   => "jstree-folder",
                "parent" => $contentParent["parent"],
                "text"   => $contentParent["description"]
            ];
        }
        
        foreach ($contentElements as $contentElement)
        {
            $jsTreeContentElementData[] = [
                "id"     => "_element#" . $contentElement["id"],
                "icon"   => "jstree-file",
                "parent" => $contentElement["parent"] ? "_parent#" . $contentElement["parent"] : $contentElement["type"],
                "text"   => $contentElement["name"]
            ];
        }
        
        return $jsTreeContentElementData;
    }
}