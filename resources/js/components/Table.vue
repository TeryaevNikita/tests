<template>
    <div>
        <b-table
            ref="mainTable"
            over
            selectable
            :items="products"
            :busy="isBusy"
            @row-selected="onRowSelected"
        >
            <template v-slot:custom-foot="row">
                <b-tr>
                    <b-td>Total (selected)</b-td>
                    <b-td>{{ totalSum }}</b-td>
                </b-tr>
            </template>
            <template v-slot:table-busy>
                <div class="text-center text-danger my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Loading...</strong>
                </div>
            </template>
        </b-table>
        <b-pagination
            v-model="currentPage"
            :total-rows="rows"
            :per-page="perPage"
            aria-controls="my-table"
            @change="onPageChange"
        ></b-pagination>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                perPage: 10,
                isBusy: false,
                currentPage: 1,
                maxItems: 0,
                totalSum: 0,
                products: [],
            }
        },
        methods: {
            loadData: function (page) {
                this.isBusy = true;

                axios.get('/api/products/', {
                    params: {
                        page: page,
                        per_page: this.perPage
                    }
                })
                    .then(response => {
                        this.products = response.data.data;
                        this.maxItems = response.data.total
                    }).catch(error => {
                    console.log(error)
                }).finally(() => this.isBusy = false)

            },
            onRowSelected(items) {
                let sum = 0;

                items.forEach((product) => {
                    sum += parseFloat(product.value);
                });

                this.totalSum = Number(sum.toFixed(2))
            },
            onPageChange(page) {
                if (page === this.currentPage) {
                    return
                }

                this.$refs.mainTable.clearSelected()
                this.loadData(page)
            }
        },
        computed: {
            rows() {
                return this.maxItems
            }
        },
        beforeMount() {
            this.loadData(1);
        }
    }
</script>
