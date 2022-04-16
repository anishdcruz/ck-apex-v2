<script type="text/javascript">
    export default {
        props: {
            id: {
                type: Number,
                required: true
            }
        },
        render: function (createElement) {
            const status = this.statusLookup(this.id)
            return createElement(
                'span',
                {
                    'class': ['status', `status-${status}`]
                },
                this.clean(status)
            )
        },
        methods: {
            statusLookup(id) {
                const list = {
                    1: 'draft',
                    2: 'sent',
                    3: 'confirmed',
                    4: 'on-hold',
                    5: 'void',
                    6: 'closed',
                    7: 'partially-issued',
                    8: 'issued',
                }
                return list[id]
            },
            clean(str) {
                return str.replace(/(\-[a-z])/g, function($1){
                    return $1.replace('-',' ')
                }).toUpperCase()
            }
        }
    }
</script>
