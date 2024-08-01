<template>
        <Header/>
        <KladrTable :pointType="'dispatch'" :pointTypeName="'отправления'"/>
        <KladrTable :pointType="'arrival'" :pointTypeName="'прибытия'"/>
</template>
<script>
import axiosAdmin from '../../axiosAdmin';
import PointExpression from '../../components/PointExpression.vue';
import KladrTable from '../../components/admin/KladrTable.vue';
import Header from '../../components/admin/Header.vue';
import { Bootstrap5Pagination } from 'laravel-vue-pagination';


export default
{
    components: { PointExpression, Bootstrap5Pagination, KladrTable, Header },
    data() {
        return {
            kladrId: '',
            kladrName: '',

            dispatchPointId: '',
            dispatchPoints: [],
            // dispatchKladrId
            // dispatchNameSearch


            arrivalPointId: '',
            arrivalPoints: [],
            // arrivalKladrId
            // arrivalDispatchPointId
            // dispatchNameSearch


            // kladrFilterValue: '',
            
            kladrs: [],
            loading: false,
            page: 1
        }
    },
    async mounted(){
        this.pointsUpdate()
        // this.getArrivals(1)
    },
    methods: {
        async kladrFilter(query){
            if(query.length <= 2){
                return
            }
            this.loading = true
            const promise =  axiosAdmin
            .get('/kladrs?kladrFilter='+query)
            .then(response => {
                this.kladrs = response.data.kladrs
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.loading = false
        },
        async pointsUpdate(page){
            this.dispatchPoints = []
            const promise1 = axiosAdmin
            .get('/dispatch_points?kladr_id='+this.kladr_id+'&')
            .then(response => {
                this.dispatchPoints = response.data
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise1  
            this.arrivalPoints = []
            // const promise2 = axiosAdmin
            // .get('/arrival_points/')
            // .then(response => {
            //     this.arrivalPoints = response.data
            //     console.log('arrival_points')
            //     console.log(response)
            // })
            // .catch(error => {
            //     console.log(error)
            // })
            // await promise2
            this.getArrivals(page)
        },
        async addDispatch(dispatchPointId, kladrId){
            const promise = axiosAdmin
            .post('/kladrs/dispatch/add', {dispatchPointId, kladrId})
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.pointsUpdate(this.page)
        },
        async addArrival(dispatchPointId, arrivalPointId, kladrId){
            const promise = axiosAdmin
            .post('/kladrs/arrival/add', {dispatchPointId, arrivalPointId, kladrId })
            .then(response => {
                console.log(response)
            })
            .catch(error => {
                console.log(error)
            })
            await promise
            this.pointsUpdate(this.page)
        },
        async getArrivals(page = 1){
            this.page = page
            const promise = axiosAdmin
            .post('/arrival/points/paginate', {page: page})
            .then(response => {
                console.log(response.data)
                this.arrivalPoints = response.data.arrivalPoints
            })
            .catch( error => {
                console.log(error)
            })
            await promise
        }
    },
    watch: {
        // async dispatchPointId(newDispatchPointId){
        //     const promise = axiosAdmin
        //     .get('/arrival_points/'+newDispatchPointId)
        //     .then(response => {
        //         this.arrivalPoints = response.data
        //         console.log(response)
        //     })
        //     .catch(error => {
        //         console.log(error)
        //     })
        //     await promise
        // },
        // kladrId(newKladrId){
        //     console.log(newKladrId)
        //     this.kladrname = this.kladrs.filter(kladr => {
        //         return kladr.id == newKladrId
        //     })[0].name
                 
        //     console.log(this.kladrname)
        //     this.pointsUpdate()
        //     // this.kladrname =

        // }
    }
}
</script>