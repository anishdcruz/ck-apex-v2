<template>
    <div class="sidebar">
        <div class="sidebar-inner">
            <div class="sidebar-links">
                <ul class="sidebar-list" v-for="list in activeList">
                    <li class="sidebar-break"></li>
                    <li v-for="link in list.links">
                        <router-link :to="link.path" class="sidebar-link">
                            <div class="sidebar-link_inner">
                                <span class="sidebar-text">{{ link.title }}</span>
                            </div>
                            <i class="fa fa-caret-right"></i>
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    export default {
        computed: {
            user() {
                return window.apex.user
            },
            activeList() {
                return this.lists.filter((item) => {
                    if(item.admin) {
                        return this.user.is_admin == true
                    } else {
                        return true
                    }
                })
            },
        },
        data() {
            return {
                lists: [
                    {
                        admin: false,
                        links: [
                            {path: '/', title: 'Dashboard', icon: 'dashboard'},
                        ]
                    },
                    {
                        admin: false,
                        links: [
                            {path: '/clients', title: 'Clients', icon: 'user'},
                            {path: '/quotations', title: 'Quotations', icon: 'file-o'},
                            {path: '/advance_payments', title: 'Advance Payments', icon: 'money'},
                            {path: '/sales_orders', title: 'Sales Orders', icon: 'file-o'},
                            {path: '/invoices', title: 'Invoices', icon: 'file-o'},
                            {path: '/client_payments', title: 'Client Payments', icon: 'money'}
                        ]
                    },
                    {
                        admin: false,
                        links: [
                            {path: '/products', title: 'Products', icon: 'cubes'},
                            {path: '/receive_orders', title: 'Receive Orders', icon: 'file-o'},
                            {path: '/goods_issue', title: 'Goods Issue', icon: 'file-o'}
                        ]
                    },
                    {
                        admin: false,
                        links: [
                            {path: '/vendors', title: 'Vendors', icon: 'users'},
                            {path: '/expenses', title: 'Expenses', icon: 'file-text-o'},
                            {path: '/purchase_orders', title: 'Purchase Orders', icon: 'file-o'},
                            {path: '/bills', title: 'Bills', icon: 'file-o'},
                            {path: '/vendor_payments', title: 'Vendor Payments', icon: 'money'}
                        ]
                    },
                    {
                        admin: true,
                        links: [
                            {path: '/settings', title: 'Settings', icon: 'cogs'},
                            {path: '/users', title: 'Users', icon: 'users'},
                        ]
                    }
                ]
            }
        }
    }
</script>
