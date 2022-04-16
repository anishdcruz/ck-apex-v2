<template>
    <div class="panel">
        <div class="panel-heading">
            <div class="panel-title">
                <slot name="title"></slot>
            </div>
            <div class="search-box">
                <input type="text" class="form-control panel-control"
                    title="Search" placeholder="Search"
                    @keyup.enter="search" v-model="params.q"
                    >
                <slot name="create"></slot>
            </div>
        </div>
        <div class="panel-body panel-min">
            <table class="table table-link">
                <thead>
                    <tr>
                        <th v-for="item in heading">
                            <div class="sort" @click="sortBy(item)">
                                <span>{{ item.title }}</span>
                                <div class="sort-direction" v-if="isSortColumn(item)">
                                    <i class="fa fa-sort-amount-desc" v-if="isSortDirection(item)"></i>
                                    <i class="fa fa-sort-amount-asc" v-else></i>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody v-if="model.data && model.data.length">
                    <slot v-for="item in model.data" :item="item"></slot>
                </tbody>
                <tbody v-else>
                    <tr>
                        <th :colspan="heading.length" class="panel-no_results">Not Results found!</th>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <div class="pagination">
                <div class="pagination-page">
                    <select class="form-control form-inline form-sm"
                        title="Items per page" @change="updatePerPage"
                        :class="[error.per_page ? 'error-bg' : '']"
                        v-model="params.per_page">
                        <option>12</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                    <small class="pagination-status">
                        Showing {{model.from}} - {{model.to}} of {{model.total}}
                    </small>
                </div>
                <div class="pagination-controls">
                    <button class="btn btn-sm" :disabled="!model.prev_page_url"
                        @click="prevPage">
                        &laquo; Prev
                    </button>
                    <button class="btn btn-sm" :disabled="!model.next_page_url"
                        @click="nextPage">
                        Next &raquo;
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    import Vue from 'vue'
    import { copyObject } from '../../lib/helpers'
    export default {
        props: {
            resource: {
                required: true,
                type: String
            },
            heading: {
                required: true,
                type: Array
            },
            defaultSortDirection: {
                type: String,
                default: 'created_at'
            },
            defaultSortColumn: {
                type: String,
                default: 'desc'
            }
        },
        data() {
            return {
                model: {
                    data: []
                },
                error: {},
                params: {
                    per_page: 12,
                    page: 1,
                    q: this.$route.query.q || '',
                    sort_column: this.$route.query.sort_column|| this.defaultSortColumn,
                    sort_direction: this.$route.query.sort_direction || this.defaultSortDirection
                }
            }
        },
        methods: {
            setData(res) {
                if(res.data) {
                    Vue.set(this.$data, 'model', res.data.data)
                    this.params.per_page = this.model.per_page
                    this.params.page = this.model.current_page
                }
                this.$bar.finish()
            },
            sortBy(item) {
                if(!item.sort) {
                    return
                }

                const query = copyObject(this.$route.query)

                query.sort_column = query.sort_column || this.params.sort_column
                query.sort_direction = query.sort_direction || this.params.sort_direction

                if(query.sort_column === item.name) {
                    query.sort_direction = query.sort_direction === 'desc'
                        ? 'asc'
                        : 'desc'
                } else {
                    query.sort_column = item.name
                    query.sort_direction = 'desc'
                }

                this.$router.push({
                    path: this.resource,
                    query: query
                })
            },
            isSortDirection(item) {
                const query = copyObject(this.$route.query)
                const direction = query.sort_direction || this.params.sort_direction

                return direction === 'desc'
            },
            isSortColumn(item) {
                const query = copyObject(this.$route.query)
                const column = query.sort_column || this.params.sort_column

                return column === item.name
            },
            search() {
                const query = Object.assign({}, this.$route.query)

                query.page = 1
                query.q = this.params.q

                this.$router.push({
                    path: this.resource,
                    query: query
                })
            },
            updatePerPage() {
                const query = copyObject(this.$route.query)

                query.per_page = this.params.per_page
                query.page = 1

                this.$router.push({
                    path: this.resource,
                    query: query
                })
            },
            nextPage() {
                if(this.model.next_page_url) {
                    const query = copyObject(this.$route.query)

                    query.page = query.page ? (Number(query.page) + 1) : 2

                    this.$router.push({
                        path: this.resource,
                        query: query
                    })
                }
            },
            prevPage() {
                if(this.model.prev_page_url) {
                    const query = copyObject(this.$route.query)

                    query.page = query.page ? (Number(query.page) - 1) : 1

                    this.$router.push({
                        path: this.resource,
                        query: query
                    })
                }
            }
        }
    }
</script>
