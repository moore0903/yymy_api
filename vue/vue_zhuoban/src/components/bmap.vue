<template>
    <!--地图容器-->
        <div id="XSDFXPage" class="XSDFXPage"></div>

</template>
<script>
    export default {
        data () {
            return {

            }
        },
        mounted() {

            //创建Map实例
            var map = new BMap.Map("XSDFXPage");
           // 初始化地图,设置中心点坐标
            var point = new BMap.Point(114.428696,30.48317);// 创建点坐标
            map.centerAndZoom(point,15);
            //添加鼠标滚动缩放
            map.enableScrollWheelZoom();

            //添加缩略图控件
            map.addControl(new BMap.OverviewMapControl({isOpen:false,anchor:BMAP_ANCHOR_BOTTOM_RIGHT}));
            //添加缩放平移控件
            map.addControl(new BMap.NavigationControl());
            //添加比例尺控件
            map.addControl(new BMap.ScaleControl());
            //添加地图类型控件
            //map.addControl(new BMap.MapTypeControl());

            //设置标注的图标
            var icon = new BMap.Icon("static/img/map2.png",new BMap.Size(34,34),{
              anchor: new BMap.Size(17, 34)
            });
            //设置标注的经纬度
            var marker = new BMap.Marker(new BMap.Point(114.428696,30.48317),{icon:icon});
            //把标注添加到地图上
            map.addOverlay(marker);
            var content = "<table>";
            content = content + "<tr><td> 武汉茁伴乐园科技有限公司</td></tr>";
            content = content + "<tr><td> 地址: 武汉市东湖新技术开发区高新二路22号中国光谷云计算海外高新企业孵化中心2栋16楼</td></tr>";
            content += "</table>";
            var infowindow = new BMap.InfoWindow(content);
            marker.addEventListener("click",function(){
                this.openInfoWindow(infowindow);
            });

            //点击地图，获取经纬度坐标
           map.addEventListener("click",function(e){
                document.getElementById("aa").innerHTML = "经度坐标："+e.point.lng+" &nbsp;纬度坐标："+e.point.lat;
            });


        }
    }
</script>
<style scoped>

</style>
