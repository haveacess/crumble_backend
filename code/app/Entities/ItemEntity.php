<?php

namespace App\Entities;

use App\Models\ItemModel;

class ItemEntity {

    public ?int $id = null;
    public int $idApp;
    public int $idClass;
    public int $idInstance;
    public string $marketHashName;

    /**
     * Get entity as model
     *
     * @return ItemModel
     */
    public function toModel(): ItemModel {
        return new ItemModel([
            'id' => $this->id,
            'id_app' => $this->idApp,
            'id_class' => $this->idClass,
            'id_instance' => $this->idInstance,
            'market_hash_name' => $this->marketHashName
        ]);
    }
}
