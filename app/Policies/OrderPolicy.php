<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('orders.view-own') || $user->hasPermissionTo('orders.view-site') || $user->hasRole('workshop');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        if ($user->hasRole('workshop')) {
            if (in_array($order->status, ['approved', 'fabricating', 'ready', 'delivered'])) {
                return true;
            }
        }

        if ($user->hasPermissionTo('orders.view-own') && $user->id === $order->created_by) {
            return true;
        }

        if ($user->hasPermissionTo('orders.view-site')) {
            if ($user->sites()->where('site_id', $order->site_id)->exists()
                || $user->managedSites()->where('id', $order->site_id)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('orders.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): bool
    {
        // Engineers can edit their own draft or rejected orders
        if ($user->hasPermissionTo('orders.edit-own') && $user->id === $order->created_by) {
            if (in_array($order->status, ['draft', 'rejected'])) {
                return true;
            }
        }

        // Managers can edit draft or submitted orders for their sites
        if ($user->hasPermissionTo('orders.edit-site')) {
            if (in_array($order->status, ['draft', 'submitted'])) {
                return $user->sites()->where('site_id', $order->site_id)->exists()
                    || $user->managedSites()->where('id', $order->site_id)->exists();
            }
        }

        return false;
    }

    public function delete(User $user, Order $order): bool
    {
        // Only engineers can delete their own draft orders
        if ($user->hasPermissionTo('orders.edit-own') && $user->id === $order->created_by) {
            if ($order->status === 'draft') {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can revert a submitted order back to draft.
     */
    public function revert(User $user, Order $order): bool
    {
        if ($user->hasPermissionTo('orders.edit-own') && $user->id === $order->created_by) {
            if ($order->status === 'submitted') {
                return true;
            }
        }

        return false;
    }
}
