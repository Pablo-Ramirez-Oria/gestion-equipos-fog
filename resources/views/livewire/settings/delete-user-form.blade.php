<section class="mt-10 space-y-6">
    @role('admin')
        <!-- Evitamos que el administrador borre su cuenta -->
    @else
    <div class="relative mb-5">
        <flux:heading>{{ __('Borrar cuenta') }}</flux:heading>
        <flux:subheading>{{ __('Borrar su cuenta y todos sus recursos') }}</flux:subheading>
    </div>

    <flux:modal.trigger name="confirm-user-deletion">
        <flux:button variant="danger" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
            {{ __('Borrar cuenta') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form wire:submit="deleteUser" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('¿Está seguro de que quiere eliminar su cuenta?') }}</flux:heading>

                <flux:subheading>
                    {{ __('Una vez su cuenta esté borrada, todos sus recursos e información será permanentemente eliminada. Por favor introduzca su contraseña para confirmar que quiere eliminar permanentemente su cuenta.') }}
                </flux:subheading>
            </div>

            <flux:input wire:model="password" :label="__('Password')" type="password" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" type="submit">{{ __('Borrar cuenta') }}</flux:button>
            </div>
        </form>
    </flux:modal>
    @endrole('admin')
</section>
