<template>
    <div class="panel">
        <div class="panel-heading">
            <div class="panel-title">
                <slot name="title"></slot>
            </div>
            <div class="search-box">
                <slot name="create"></slot>
            </div>
        </div>
        <div class="panel-body panel-mini">
            <table class="table table-link">
                <thead>
                    <tr>
                        <th v-for="column in heading">{{column}}</th>
                    </tr>
                </thead>
                <tbody v-if="model.data && model.data.length">
                    <slot v-for="item in model.data" :item="item"></slot>
                </tbody>
                <tbody v-else>
                    <tr>
                        <th :colspan="heading.length" class="table-no_results">Not Results found!</th>
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
                        <option>5</option>
                        <option>10</option>
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
    import { get } from '../../lib/api'
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
                    per_page: 5,
                    page: 1,
                    q: null,
                    sort_column: null,
                    sort_direction: null
                }
            }
        },
        created() {
            this.fetchData()
        },
        methods: {
            fetchData() {
                get(this.resource, this.params)
                    .then(({ data }) => {
                        Vue.set(this.$data, 'model', data.model)
                        this.params.per_page = this.model.per_page
                        this.params.page = this.model.current_page
                        this.params.q = ''
                        this.params.sort_column = this.defaultSortColumn
                        this.params.sort_direction = this.defaultSortDirection
                    })
            },
            sortBy(item) {
                if(!item.sort) {
                    return
                }

                if(this.params.sort_column === item.name) {
                    this.params.sort_direction = this.params.sort_direction === 'desc'
                        ? 'asc'
                        : 'desc'
                } else {
                    this.params.sort_column = item.name
                    this.params.sort_direction = 'desc'
                }

                this.fetchData()
            },
            isSortDirection(item) {
                const direction = this.params.sort_direction
                return direction === 'desc'
            },
            isSortColumn(item) {
                const column = this.params.sort_column
                return column === item.name
            },
            updatePerPage() {
                this.params.page = 1
                this.fetchData()
            },
            nextPage() {
                if(this.model.next_page_url) {
                    this.params.page++
                    this.fetchData()
                }
            },
            prevPage() {
                if(this.model.prev_page_url) {
                    this.params.page--
                    this.fetchData()
                }
            },
        }
    }
</script>
