<?php

namespace Support\CQRS;

use Aws\DynamoDb\BinaryValue;
use BaoPham\DynamoDb\DynamoDbModel;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class DynamoDbEventModel extends DynamoDbModel
{
    protected $table = 'events';
    public $timestamps = false;
    protected $dynamoDbIndexKeys = [
        'entity_index' => [
            'hash' => 'entityId',
        ],
    ];
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'id',
        'topic',
        'entityId',
        'eventData',
        'published',
        'created_at',
    ];

    /**
     * @var BinaryValue|mixed
     */
    public mixed $id;

    /**
     * @var BinaryValue|mixed
     */
    public mixed $entityId;

    /**
     * @var string
     */
    public string $topic;

    /**
     * @var boolean
     */
    public mixed $published;

    /**
     * @var int
     */
    public mixed $created_at;

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            $model->fill([
                'id'        => new BinaryValue(Uuid::uuid4()->getBytes()),
                'published' => false,
            ]);
        });

        static::saving(function (self $model) {
            if (! isset($model->created_at) || ! $model->created_at) {
                $model->fill([
                    'created_at' => Carbon::now()->timestamp,
                ]);
            }
        });
    }

    public function setPublished(bool $published) : self
    {
        $this->fill([
            'published' => $published,
        ])->save();

        return $this;
    }
}
