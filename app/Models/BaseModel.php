<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseModel extends Model
{
    use SoftDeletes;
    /**
     * Kolom organization default
     * Bisa dioverride di child model
     */
    protected string $organizationColumn = 'id_organization';

    /**
     * Aktifkan timestamps ala Laravel
     */
    public $timestamps = true;

    /**
     * Boot model (global scope & events)
     */
    protected static function booted(): void
    {
        // ============================
        // GLOBAL SCOPE ORGANIZATION
        // ============================
        static::addGlobalScope('organization', function (Builder $builder) {
            $orgId = static::getOrganizationId();

            if (!is_null($orgId)) {
                $builder->where(
                    (new static)->getOrganizationColumn(),
                    $orgId
                );
            }
        });

        // ============================
        // AUTO SET ORGANIZATION ON CREATE
        // ============================
        static::creating(function (Model $model) {
            $column = $model->getOrganizationColumn();

            if (
                $column &&
                empty($model->{$column})
            ) {
                $model->{$column} = static::getOrganizationId();
            }
        });
    }

    /**
     * Ambil organization id dari context (middleware)
     */
    protected static function getOrganizationId(): ?int
    {
        return app()->bound('id_organization')
            ? (int) app('id_organization')
            : null;
    }

    /**
     * Getter nama kolom organization
     */
    public function getOrganizationColumn(): ?string
    {
        return property_exists($this, 'organizationColumn')
            ? $this->organizationColumn
            : null;
    }

    /**
     * Scope untuk override organization
     * Contoh: Model::forOrganization(99)->get();
     */
    public function scopeForOrganization(
        Builder $query,
        int $organizationId
    ): Builder {
        return $query
            ->withoutGlobalScope('organization')
            ->where($this->getOrganizationColumn(), $organizationId);
    }

    /**
     * Scope untuk disable organization filter
     * Contoh: Model::withoutOrganization()->get();
     */
    public function scopeWithoutOrganization(Builder $query): Builder
    {
        return $query->withoutGlobalScope('organization');
    }
}
