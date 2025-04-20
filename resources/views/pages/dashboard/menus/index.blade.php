@section('title', $data['title'] ?? '')
@section('meta_description', '')

<x-app-layout>
    <section class="p-1 px-4 md:py-2">
        <div class="mb-4 text-2xl font-semibold">
            {{ $data['title'] ?? 'Menu Builder' }}
        </div>

        <x-card>
            <form id="form-menu">
                @csrf
                <div class="mb-3">
                    <x-dashboard.input-label for="name" value="{{ __('Menu name') }}" />
                    <x-dashboard.text-input class="p-1!" id="menu-name" name="name" type="text" placeholder="Menu Name" />
                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
                <div class="mb-3">
                    <x-dashboard.input-label for="location" value="{{ __('Menu Location') }}" />
                    <select class="w-full rounded-md border-gray-300" id="menu-location" name="location">
                        <option value="">-- Select Location --</option>
                        <option value="header">Header</option>
                        <option value="header-top">Header-Top</option>
                        <option value="footer-a">Footer-A</option>
                        <option value="footer-b">Footer-B</option>
                    </select>
                    <x-dashboard.input-error class="mt-2" :messages="$errors->get('location')" />
                </div>
                <x-dashboard.primary-button type="submit" size="small">Create</x-dashboard.primary-button>
            </form>
        </x-card>
    </section>

    <section class="p-1 px-4 md:py-2">
        <div class="grid grid-cols-1 gap-2 lg:grid-cols-3 lg:gap-4">
            <div>
                <x-card class="mb-2">
                    <div class="mb-3">
                        <h4 class="mb-0 text-lg">Menu List</h4>
                    </div>
                    <div class="space-y-2 text-sm text-gray-700" id="menu-list">
                        <div class="italic text-gray-400">Loading menu list...</div>
                    </div>
                </x-card>
                <x-card>
                    <div class="mb-3">
                        <h4 class="mb-0 text-lg">Exsisting Link</h4>
                    </div>
                    <div class="max-h-48 min-h-12 overflow-y-auto text-sm" id="existing-link">
                        <p>Loading existing links... (comming soon)</p>
                    </div>
                </x-card>
            </div>
            <div class="lg:col-span-2">
                <x-card>
                    <div class="mb-3">
                        <h4 class="mb-0 text-lg">Manage Menu</h4>
                    </div>

                    <div class="text-sm italic" id="menu-detail">
                        Please select a menu to manage its items.
                    </div>
                </x-card>
            </div>
        </div>
    </section>

    @push('javascript')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

        <script>
            // Load all menus on sidebar
            function loadMenus() {
                $.get("{{ route('admin.settings.menu.list') }}", function(data) {
                    const list = $('#menu-list').empty();
                    if (!data.length) {
                        list.append('<div class="text-gray-400 italic">No menus found.</div>');
                        return;
                    }

                    data.forEach(menu => {
                        const location = menu.location ? `<span class='text-gray-500 text-xs ml-2'>[${menu.location}]</span>` : '';
                        list.append(`
                        <div class="cursor-pointer hover:underline text-blue-600" data-location="${menu.location}" data-id="${menu.id}">
                            ${menu.name} (${menu.items_count} item) ${location}
                            <button class="text-sm text-red-500 float-right hover:text-red-800 delete-menu" data-id="${menu.id}">
                                <i class="ri-delete-bin-line"></i>
                            </button>
                        </div>
                `);
                    });
                });
            }

            // Handle menu creation
            $('#form-menu').submit(function(e) {
                e.preventDefault();
                $.post("{{ route('admin.settings.menu.create') }}", {
                    name: $('#menu-name').val(),
                    location: $('#menu-location').val()
                }).done(res => {
                    $('#menu-name, #menu-location').val('');
                    $('#name-error, #location-error').text('');
                    loadMenus();
                    $('#menu-detail').html(`<div class="text-green-600">Menu "${res.menu.name}" created. Select from the list to manage items.</div>`);
                }).fail(err => {
                    if (err.responseJSON.errors?.name) {
                        $('#name-error').text(err.responseJSON.errors.name[0]);
                        MyZkToast.error(err.responseJSON.errors.name[0]);
                    }
                    if (err.responseJSON.errors?.location) {
                        $('#location-error').text(err.responseJSON.errors.location[0]);
                        MyZkToast.error(err.responseJSON.errors.location[0]);
                    }
                });
            });

            // Fetch and render selected menu items
            $('#menu-list').on('click', '[data-id]', function() {
                const menuId = $(this).data('id');
                MyZkToast.info('Fetching menu items...');

                $.get(`{{ route('admin.settings.menu.items', ['menu' => ':id']) }}`.replace(':id', menuId), function(res) {
                    console.log(res);

                    if (!res.menu || !res.menu.name) return MyZkToast.error('Menu not found.');

                    const html = `
                        <div class="mb-4">
                            <h5 class="text-xl font-semibold mb-2">Manage Menu: ${res.menu.name}</h5>
                            <form id="form-add-item" class="space-y-2 mb-4">
                                <input type="hidden" name="menu" value="${menuId}">
                                <x-dashboard.text-input class="p-1!" name="label" type="text" placeholder="Label" required></x-dashboard.text-input>
                                <x-dashboard.text-input class="p-1!" name="link" type="text" placeholder="Link (e.g., /about)" required></x-dashboard.text-input>
                                <x-dashboard.primary-button type="submit" size="small">Add Item</x-dashboard.primary-button>
                            </form>
                        </div>
                        <ul id="sortable-menu-items" class="nested-sortable space-y-2">${renderItems(res.items)}</ul>

                        <button id="save-menu-structure" class="mt-4 px-4 py-2 text-white bg-back-success hover:bg-back-success/80 rounded">💾 Save Order</button>
                    `;

                    $('#menu-detail').html(html);
                    initSortable();
                }).fail(() => MyZkToast.error('Failed to fetch menu details.'));
            });

            // Render nested items recursively
            function renderItems(items, parentId = null) {
                const nested = items
                    .filter(item => item.parent == parentId)
                    .sort((a, b) => a.sort - b.sort)
                    .map(item => `
                        <li class="p-2 bg-gray-50 rounded border" data-id="${item.id}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="handle cursor-move text-gray-400">☰</span>
                                    <div>
                                        <strong>${item.label}</strong>
                                        <span class="text-sm text-gray-500 ml-2">(${item.link})</span>
                                    </div>
                                </div>
                                <button class="text-red-500 text-sm delete-item" data-id="${item.id}">✕</button>
                            </div>
                            <ul class="nested-sortable space-y-2 pl-4 mt-2">
                                ${renderItems(items, item.id)}
                            </ul>
                        </li>
            `);
                return nested.join('');
            }

            // Add menu item
            $('#menu-detail').on('submit', '#form-add-item', function(e) {
                e.preventDefault();
                const form = $(this),
                    data = form.serialize();

                $.post("{{ route('admin.settings.menu.storeItem') }}", data)
                    .done(res => {
                        MyZkToast.success(res.message || 'Item added.');
                        console.log(res);
                        $(`[data-location="${res.location}"]`).click(); // Reload the selected menu
                        loadMenus();
                    })
                    .fail(err => MyZkToast.error(err.responseJSON.message || 'Failed to add item.'))
                    .always(() => {
                        form.prop('disabled', false);
                        form.find('button').html('Add Item');
                    });

                form.prop('disabled', true);
                form.find('button').html('Adding...');
            });

            // Save item menu structure
            $('#menu-detail').on('click', '#save-menu-structure', function() {
                const structure = getStructure($('#sortable-menu-items'));
                $.post("{{ route('admin.settings.menu.updateStructure') }}", {
                        structure
                    })
                    .done(res => {
                        MyZkToast.success(res.message || 'Saved.');
                        // Trigger ulang fetch menu item berdasarkan ID menu yang baru saja diperbarui
                        $('[data-location="' + res?.location + '"]').click();
                        loadMenus();
                    })
                    .fail(err => MyZkToast.error(err.responseJSON.message || 'Failed to save.'))
                    .always(() => $('#save-menu-structure').prop('disabled', false).html('💾 Save Order'));
                $(this).prop('disabled', true).html('Saving...');
            });

            // Delete menu item
            $('#menu-detail').on('click', '.delete-item', function() {
                const itemId = $(this).data('id'),
                    el = $(this).closest('li');
                if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: "{{ route('admin.settings.menu.deleteItem', ['item' => ':id']) }}".replace(':id', itemId),
                        type: 'DELETE'
                    }).done(res => {
                        MyZkToast.success(res.message || 'Item deleted.');
                        el.remove();
                        loadMenus();
                    }).fail(err => MyZkToast.error(err.responseJSON.message || 'Failed to delete item.'));
                }
            });

            // Delete menu
            $('#menu-list').on('click', '.delete-menu', function() {
                const menuId = $(this).data('id'),
                    el = $(this).closest('.cursor-pointer');
                if (confirm('Are you sure you want to delete this menu?')) {
                    $.ajax({
                        url: "{{ route('admin.settings.menu.delete', ['menu' => ':id']) }}".replace(':id', menuId),
                        type: 'DELETE'
                    }).done(res => {
                        MyZkToast.success(res.message || 'Menu deleted.');
                        el.remove();
                        $('#menu-detail').html('');
                    }).fail(err => MyZkToast.error(err.responseJSON.message || 'Failed to delete menu.'));
                }
            });

            // Init nested Sortable
            function initSortable() {
                document.querySelectorAll('.nested-sortable').forEach(el => {
                    new Sortable(el, {
                        group: 'nested',
                        animation: 150,
                        fallbackOnBody: true,
                        swapThreshold: 0.65,
                        ghostClass: 'bg-yellow-100',
                        handle: '.handle',
                        draggable: 'li'
                    });
                });
            }

            // Recursive structure parser
            function getStructure(el, parentId = 0, depth = 0) {
                const structure = [];
                $(el).children('li').each(function(index) {
                    const id = $(this).data('id');
                    structure.push({
                        id,
                        sort: index,
                        parent: parentId,
                        depth
                    });

                    const children = $(this).children('ul.nested-sortable');
                    if (children.length) {
                        structure.push(...getStructure(children, id, depth + 1));
                    }
                });
                return structure;
            }

            // Load initial menus
            loadMenus();
        </script>
    @endpush
</x-app-layout>
