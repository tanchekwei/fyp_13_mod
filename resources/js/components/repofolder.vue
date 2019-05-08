<template>
    <div>
        <tree
                :data="repository"
                :options="treeOptions"
                @node:selected="onNodeSelected"
                @node:dblclick="onNodedblclick"
        />

    </div>
</template>

<script>
    import Vue from 'Vue'
    import LiquorTree from 'liquor-tree'

    Vue.use(LiquorTree)

    export default {
        props: ['repository'],

        data: function() {
            return {
                treeData: [
                    { text: 'Item 1' },
                    { text: 'Item 2', children: [
                            { text: 'Item 2.1' },
                            { text: 'Item 2.2' },
                            { text: 'Item 2.3' }
                        ]},
                    { text: 'Item 3' },
                    { text: 'Item 4' }
                ],
                treeOptions: {
                }
            }
        },
        methods: {
            onNodeSelected(node) {
                //console.log(node.text)
            },
            onNodedblclick(node) {
                //console.log(node.data.blob);
                if(!node.hasChildren()) {
                    //console.log('can post')
                    var id = window.location.href.split('/');
                    //console.log(id[6]);
                    var url = 'http://i2hub.tarc.edu.my:8468/viewfile/' + id[5] + '/' + id[6] + '/' + node.data.blob;
                    window.open(url, '_blank');
                }
            }
        },
        mounted() {
            //console.log();
            //this.treeData = [{text: 'Item 1'}];
        }
    }

</script>