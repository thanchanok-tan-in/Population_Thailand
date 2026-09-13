
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Geolocation</title>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <style>
            #mapid {
                height: 100vh;  /* ความสูงเต็มหน้าจอ  100vh */
                width: 100vw;   /* ความกว้างเต็มหน้าจอ  100vw*/
                margin: 0;
                padding: 0;
            }
            body, html {
                height: 100%;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .info { 
            padding: 6px 8px; 
            font: 14px/16px Arial, Helvetica, sans-serif; 
            background: white; 
            background: rgba(255,255,255,0.8); 
            box-shadow: 0 0 15px rgba(0,0,0,0.2); 
            border-radius: 5px; 
            } 

            .info h4 { margin: 0 0 5px; color: #777; }

            .legend { 
            text-align: left; 
            line-height: 18px; 
            color: #555; 
            } 

            .legend i { 
            width: 18px; 
            height: 18px; 
            float: left; 
            margin-right:8px; 
            opacity: 0.7; 
            }

            
            /* style สำหรับ popup */
            .leaflet-popup-content-wrapper {
                border-radius: 10px;
                background: #f9f9f9;
                box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
                padding: 10px;
            }

            .leaflet-popup-content {
                margin: 0;
            }

            .popup-card {
                font-family: "Arial", sans-serif;
                font-size: 14px;
                color: #333;
            }

            .popup-card h4 {
                margin: 0 0 10px;
                padding-bottom: 5px;
                font-size: 16px;
                font-weight: bold;
                border-bottom: 1px solid #ddd;
                color: #2c3e50;
            }

            .popup-card table {
                width: 100%;
                border-collapse: collapse;
            }

            .popup-card table td {
                padding: 4px 6px;
                vertical-align: top;
            }

            .popup-card table td:first-child {
                font-weight: bold;
                color: #555;
            }


            /* KPI */
            #side-card {
                position: absolute;
                top: 80px;        /* ระยะจากด้านบน */
                left: 20px;       /* ✅ ย้ายมาอยู่ด้านซ้าย */
                width: 300px;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
                padding: 16px;
                font-family: Arial, sans-serif;
                z-index: 9999;    /* ให้อยู่เหนือ map */
            }

            #side-card h3 {
                margin-top: 0;
                color: #2e2d2a;
            }

            #side-card p {
                margin: 4px 0;
            }

            /* จัดเรียงแนวตั้ง */
            #kpi-card {
                display: flex;
                flex-direction: column;
                gap: 10px;
                position: absolute;
                top: 130px;
                left: 20px;
                z-index: 9999;
            }

            .side-card {
                width: 200px;
                max-width: 50vw;
                padding: 16px;
                border-radius: 12px;
                font-family: Arial, sans-serif;
                box-shadow: 0 4px 8px rgba(0,0,0,0.5);
                backdrop-filter: blur(5px);
                line-height: 0.5; /* ลดระยะห่างบรรทัด */
            }

            .card-bg-1 { background-color: rgba(255,255,255,0.7); }
            .card-bg-2 { background-color: rgba(255,255,255,0.5); }

            /* เว้นระยะ dropdown กับข้อความด้านล่าง */
            #region-select {
                width: 100%;          /* ให้เต็ม card */
                padding: 6px 8px;
                margin-top: 5px;      /* เว้นระยะจาก label ข้างบน */
                margin-bottom: 2px;  /* เว้นระยะจากข้อความด้านล่าง */
                border-radius: 6px;
                border: 1px solid #ccc;
                font-size: 14px;
            }

            /* เว้นระยะ label จากหัวข้อ */
            .side-card label {
                font-weight: bold;
                display: block;
                margin-bottom: 4px;
            }

            .side-card {
                background: rgba(249, 249, 249, 0.8); /* พื้นหลังคล้าย popup */
                border-radius: 10px;
                box-shadow: 0px 4px 10px rgba(0,0,0,0.3); /* เงา popup */
                padding: 12px 16px;
                font-family: Arial, sans-serif;
                line-height: 1.3; /* ลดระยะบรรทัดให้เหมือน popup */
                color: #333;
                min-width: 250px;
                max-width: 320px;
            }

            /* Header ของ card */
            .card-header {
                font-weight: bold;
                font-size: 16px;
                margin-bottom: 6px;
            }

            /* สีเหมือน popup */
            .card-body p strong {
                display: inline-block;
                min-width: 50px;
            }
            /*
            .card-body p.total { color: #eb801bff; }
            .card-body p.male  { color: #78A3D4; }
            .card-body p.female{ color: #FB6F92; }
            /*

            /* ตาราง top 5 */
            #province-table {
                border-collapse: collapse;
                width: 100%;
                font-size: 14px;
            }
            #province-table th, #province-table td {
                border: 1px solid #ddd;
                padding: 6px;
            }
            #province-table th {
                background: rgba(239, 239, 239, 0.9);
                font-weight: bold;
            }




        </style>
    </head>

    <body>

        <div class="container">
            <div class="row col-12" style=" display: flex; justify-content: center; align-items: center;">
                
                <!-- KPI card -->
                <div id="kpi-card">

                    <!-- Total Thailand -->
                    <div class="side-card" style="border: 3px solid #800026;">
                        <div class="card-header">Thailand Population</div>
                        <div class="card-body">
                            <p class="total">
                                <img src="./images/3_peple.png" alt="Total" style="width:20px; vertical-align:middle; margin-right:5px;">
                                <span id="total-pop"></span>
                            </p>
                            <p class="male">
                                <img src="./images/1_1_man.png" alt="Male" style="width:25px; vertical-align:middle; margin-right:5px;">
                                <span id="male-pop"></span>
                            </p>
                            <p class="female">
                                <img src="./images/2_1_woman.png" alt="Female" style="width:25px; vertical-align:middle; margin-right:5px;">
                                <span id="female-pop"></span>
                            </p>
                        </div>
                    </div>


                    <!-- Population by Region -->
                    <div class="side-card" style="border: 3px solid #E31A1C;">
                        <div class="card-body">
                            <label for="region-select">Select Region:</label>
                            <select id="region-select">
                                <option value="กรุงเทพมหานครและปริมณฑล" selected>Bangkok and Vicinities</option>
                                <option value="ภาคกลาง">Central Region</option>
                                <option value="ภาคตะวันออก">Eastern Region</option>
                                <option value="ภาคตะวันตก">Western Region</option>
                                <option value="ภาคเหนือ">Northern Region</option>
                                <option value="ภาคตะวันออกเฉียงเหนือ">Northeastern Region</option>
                                <option value="ภาคใต้">Southern Region</option>
                            </select>

                            <p class="total">
                                <img src="images/3_peple.png" alt="Total" style="width:20px; vertical-align:middle; margin-right:5px;">
                                <span id="region-total"></span>
                            </p>
                            <p class="male">
                                <img src="images/1_1_man.png" alt="Male" style="width:20px; vertical-align:middle; margin-right:5px;">
                                <span id="region-male"></span>
                            </p>
                            <p class="female">
                                <img src="images/2_1_woman.png" alt="Female" style="width:20px; vertical-align:middle; margin-right:5px;">
                                <span id="region-female"></span>
                            </p>
                        </div>
                    </div>


                     <!-- Top 5 Provinces -->
                    <!-- Top 5 Provinces -->
                    <div class="side-card" style="border: 3px solid #FD8D3C;">
                        <div class="card-header">Top 5 Provinces</div>
                        <div class="card-body">
                            <table id="province-table">
                                <thead>
                                    <tr>
                                        <th>Province</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- แถวจะถูกเติมด้วย JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>


                </div>


               <!-- Search Box กลางล่าง -->
                <div id="search-container" style="
                    position: absolute; 
                    bottom: 30px; left: 50%; 
                    transform: translateX(-50%);
                    z-index: 1000; 
                    min-width: 260px;
                ">

                    <!-- Error message -->
                    <div id="search-error" style="color:red; 
                            font-size:12px; 
                            margin-top:4px;
                            margin-bottom:4px;
                    ">
                    </div>

                    <!-- กล่อง input + icon -->
                    <div style="position: relative; 
                            display: flex; 
                            align-items: center; 
                            background: #814256; 
                            border-radius: 8px; 
                            padding: 4px;">
                        <!-- ไอคอน -->
                        <svg viewBox="0 0 20 20" aria-hidden="true" style="
                            position: relative; 
                            width: 16px; 
                            height: 16px; 
                            fill: #fff;
                            margin-left: 4px;
                        ">
                            <path d="M16.72 17.78a.75.75 0 1 0 1.06-1.06l-1.06 1.06ZM9 14.5A5.5 5.5 0 0 1 3.5 9H2a7 7 0 0 0 7 7v-1.5ZM3.5 9A5.5 5.5 0 0 1 9 3.5V2a7 7 0 0 0-7 7h1.5ZM9 3.5A5.5 5.5 0 0 1 14.5 9H16a7 7 0 0 0-7-7v1.5Zm3.89 10.45 3.83 3.83 1.06-1.06-3.83-3.83-1.06 1.06ZM14.5 9a5.48 5.48 0 0 1-1.61 3.89l1.06 1.06A6.98 6.98 0 0 0 16 9h-1.5Zm-1.61 3.89A5.48 5.48 0 0 1 9 14.5V16a6.98 6.98 0 0 0 4.95-2.05l-1.06-1.06Z"></path>
                        </svg>

                        <!-- Input -->
                        <input type="text" id="province-search" placeholder="ค้นหาจังหวัด..." style="
                            flex: 1;
                            padding: 6px 8px 6px 8px;
                            margin-left: 4px;
                            border: none;
                            border-radius: 0 8px 8px 0;
                            outline: none;
                        ">
                    </div>

                    <!-- Autocomplete list -->
                    <div id="autocomplete-list" style="
                        position: absolute;
                        bottom: 36px;
                        left: 50%;
                        transform: translateX(-50%);
                        background: #fff;
                        border: 1px solid #ccc;
                        max-height: 150px;
                        overflow-y: auto;
                        width: 230px;
                        display: none;
                        border-radius: 8px 8px 0 0;
                        z-index: 1001;
                    "></div>

                </div>


                


                    <!-- แมพ -->
                <div class="col-md-12 pt-3 col-12">
                    <div class="card md-4">
                        <div class="card-body">

                            <!-- แมพ -->
                            <div id="map-container" style="position: relative;">
                                
                                <!-- ชื่อแมพ -->
                                <div id="map-title" style="
                                position: absolute;
                                top: 20px;
                                left: 50px;
                                z-index: 1000;
                                font-weight: bold;
                                font-size: 36px;
                                text-align: left;
                                line-height: 1.2;
                                font-family: 'Segoe UI', Tahoma, sans-serif;
                            ">
                                <!-- Population -->
                                <span style="
                                    color: #CE6857; /* 🔴 แดงเข้ม */
                                    text-shadow: 2px 2px 5px rgba(255, 255, 255, 0.6); /* เงาดำชัด */
                                ">
                                    Population
                                </span>

                                <!-- from -->
                                <span style="
                                    font-size: 24px;
                                    color: #523E27; /* ⚫ เทาเข้ม */
                                    font-weight: 600;
                                    margin-left: 6px;
                                    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
                                "> 
                                    from
                                </span><br>

                                <!-- Registration in 2023 -->
                               <span style="
                                    color: #523E27; /* 💙 น้ำเงินกรมท่า */
                                    font-size: 28px;
                                    font-weight: 700;
                                    text-shadow: 2px 2px 6px rgba(255, 255, 255, 0.7);
                                    white-space: nowrap;
                                ">
                                    Registration in <span style="font-size: 35px; color: #A87008;">2023</span>
                                </span>

                            </div>



                                <!-- ตัวแมพ -->
                                <div id="mapid"></div>

                            </div>

                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        



    </body>

    <!-- Resources -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

        <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
        <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
        <script src="https://cdn.amcharts.com/lib/4/plugins/wordCloud.js"></script>
        <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>


    
    <script>
        $(document).ready(function() {

            // โหลด population.json
            $.getJSON("./population.json", function(pop_data) {

                // โหลด GeoJSON
                $.getJSON("./7days.json", function(geojson_data) {

                    // รวม population เข้ากับ GeoJSON
                    geojson_data.features.forEach(function(feature) {
                        var popItem = pop_data.find(p => p.Province.toLowerCase() === feature.properties.name.toLowerCase());
                        feature.properties.p = popItem ? popItem["ประชากรรวม (Total Population)"] : 0;
                        feature.properties.popItem = popItem; // เก็บ object ไว้ใช้ใน popup
                    });

                    // สร้าง map
                    var map = L.map('mapid').setView([13, 101.5], 5);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="http://openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    // info control
                    var info = L.control();
                    info.onAdd = function (map) {
                        this._div = L.DomUtil.create('div', 'info');
                        this.update();
                        return this._div;
                    };
                    info.update = function (props) {
                        this._div.innerHTML = '<h4>Thai Population Density</h4>' +
                            (props ? '<b>' + props.name + '</b><br />' + props.p.toLocaleString() + ' people' : 'Hover over a state');
                    };
                    info.addTo(map);



                        
                    

                    
                    // === Province Search Feature ===
                    const searchInput = document.getElementById("province-search");
                    const autocompleteList = document.getElementById("autocomplete-list");
                    const searchError = document.getElementById("search-error");

                    // สร้าง Array ของจังหวัดจาก population.json
                    let provinceList = pop_data.map(d => ({
                        thai: d.จังหวัด,
                        eng: d.Province,
                        data: d
                    }));

                    searchInput.addEventListener("input", function() {
                        const query = this.value.trim().toLowerCase();
                        autocompleteList.innerHTML = "";
                        searchError.innerText = "";

                        if (!query) {
                            autocompleteList.style.display = "none";
                            return;
                        }

                        // filter เฉพาะชื่อที่ขึ้นต้นตรงกับ query (prefix match)
                        let matches = provinceList.filter(p =>
                            p.thai.toLowerCase().startsWith(query) || p.eng.toLowerCase().startsWith(query)
                        );

                        if (matches.length === 0) {
                            autocompleteList.style.display = "none";
                            searchError.innerText = "ไม่พบจังหวัดที่ตรงกับคำค้น";
                            return;
                        }

                        // เรียงลำดับตามชื่อไทย
                        matches.sort((a, b) => a.thai.localeCompare(b.thai, 'th'));

                        // แสดงเฉพาะ 5 อันดับแรก
                        matches.slice(0, 5).forEach(p => {
                            const div = document.createElement("div");
                            div.style.padding = "4px";
                            div.style.cursor = "pointer";
                            div.innerText = `${p.thai} (${p.eng})`;

                            div.addEventListener("click", function() {
                                autocompleteList.innerHTML = "";
                                autocompleteList.style.display = "none";
                                searchInput.value = `${p.thai} (${p.eng})`;

                                // ค้นหา feature ใน GeoJSON
                                let feature = geojson_data.features.find(f => 
                                    f.properties.name.toLowerCase() === p.eng.toLowerCase()
                                );

                                if (feature) {
                                    let layer = geojson.getLayers().find(l => l.feature.properties.name === feature.properties.name);
                                    if (layer) {
                                        map.fitBounds(layer.getBounds());
                                        layer.openPopup();
                                    }
                                } else {
                                    searchError.innerText = "ไม่พบพิกัดของจังหวัดนี้";
                                }
                            });

                            autocompleteList.appendChild(div);
                        });

                        autocompleteList.style.display = "block";
                    });

                    // ปิด autocomplete เมื่อคลิกนอก
                    document.addEventListener("click", function(e){
                        if(!document.getElementById('search-container').contains(e.target)){
                            autocompleteList.innerHTML = "";
                            autocompleteList.style.display = "none";
                        }
                    });








                    // ✅ ฟังก์ชันรวมประชากรทั้งประเทศ
                    function calculateNationalPopulation(features) {
                        let total = 0;
                        let male = 0;
                        let female = 0;

                        features.forEach(f => {
                            let p = f.properties.popItem;
                            if (p) {
                                total  += p["ประชากรรวม (Total Population)"] || 0;
                                male   += p["ประชากรชาย (Male population)"] || 0;
                                female += p["ประชากรหญิง (Female population)"] || 0;
                            }
                        });

                        return { total, male, female };
                    }

                    
                    // search region
                    let regionData = [];

                    // โหลด population.json
                    $.getJSON("./population.json", function(pop_data){
                        regionData = pop_data;

                        // แสดงค่าเริ่มต้น
                        updateRegion("กรุงเทพมหานครและปริมณฑล");
                    });

                    // ฟังก์ชันอัปเดต KPI ตามภูมิภาค
                    function updateRegion(region){
                        // filter หา object ของภาคนั้นโดยตรง
                        let data = regionData.find(p => p.ภาค === region);

                        document.getElementById("region-total").innerText  = data ? data["ประชากรรวม (Total Population)"].toLocaleString() : "-";
                        document.getElementById("region-male").innerText   = data ? data["ประชากรชาย (Male population)"].toLocaleString() : "-";
                        document.getElementById("region-female").innerText = data ? data["ประชากรหญิง (Female population)"].toLocaleString() : "-";
                    }

                    // เปลี่ยนเมื่อเลือก dropdown
                    $('#region-select').on('change', function(){
                        updateRegion($(this).val());
                    });




                    // ฟังก์ชันหา top N จังหวัด
                    function getTopProvinces(features, topN = 5) {
                        return features
                            .map(f => {
                                let p = f.properties.popItem;
                                return {
                                    name: f.properties.name,
                                    total: p ? p["ประชากรรวม (Total Population)"] : 0
                                };
                            })
                            .sort((a,b) => b.total - a.total)
                            .slice(0, topN);
                    }

                    // ฟังก์ชันเว้นวรรคชื่อจังหวัดเมื่อเจอตัวใหญ่
                    function formatProvinceName(name) {
                        if (!name) return "";
                        // ใส่ช่องว่างหน้าตัวอักษรใหญ่ (ยกเว้นตัวแรก)
                        return name.replace(/([A-Zก-ฮ])/g, ' $1').trim();
                    }

                    // แสดงลงตาราง
                    function renderTopProvincesTable(topProvs) {
                        const tbody = document.querySelector("#province-table tbody");
                        tbody.innerHTML = ""; // ล้างก่อน

                        topProvs.forEach((p, index) => {
                            const tr = document.createElement("tr");
                            const provinceName = formatProvinceName(p.name); // ใช้ฟังก์ชันเว้นวรรค

                            if(index === 0){
                                // อันดับ 1 เน้นสีแดง ตัวหนา
                                tr.innerHTML = `<td style="color: #EB4343; font-weight:bold;">${provinceName}</td>
                                                <td style="color: #EB4343; font-weight:bold;">${p.total.toLocaleString()}</td>`;
                            } else {
                                tr.innerHTML = `<td>${provinceName}</td><td>${p.total.toLocaleString()}</td>`;
                            }
                            tbody.appendChild(tr);
                        });
                    }

                    // เรียกฟังก์ชัน
                    const top5 = getTopProvinces(geojson_data.features, 5);
                    renderTopProvincesTable(top5);








                    






                    // ✅ เรียกใช้หลังจากโหลด geojson_data เสร็จ
                    let nationalData = calculateNationalPopulation(geojson_data.features);

                    // ✅ อัปเดตลง HTML
                    document.getElementById("total-pop").innerText  = nationalData.total.toLocaleString();
                    document.getElementById("male-pop").innerText   = nationalData.male.toLocaleString();
                    document.getElementById("female-pop").innerText = nationalData.female.toLocaleString();
                   


                    //ช่วงต่างกัน
                    /*
                    function getColor(d) {
                        return  d > 2000000 ? '#800026' :
                                d > 1500000 ? '#BD0026' :
                                d > 1000000 ? '#E31A1C' :
                                d > 750000  ? '#FC4E2A' :
                                d > 500000  ? '#FD8D3C' :
                                d > 300000  ? '#FEB24C' :
                                d > 200000  ? '#FED976' :
                                            '#FFEDA0';
                    }
                    */

                    /* 
                    // ช่วงเท่ากัน
                    function getColor(d) {
                        return  d > 4710000 ? '#800026' :  // 4.71M+
                                d > 4050000 ? '#BD0026' :  // 4.05M+
                                d > 3390000 ? '#E31A1C' :  // 3.39M+
                                d > 2730000 ? '#FC4E2A' :  // 2.73M+
                                d > 2070000 ? '#FD8D3C' :  // 2.07M+
                                d > 1410000 ? '#FEB24C' :  // 1.41M+
                                d > 750000  ? '#FED976' :  // 0.75M+
                                            '#FFEDA0';   // ต่ำกว่า 0.75M
                    }
                    */

                    function getColor(d) {
                        return d > 4000000 ? '#800026' :  // Top ~10%
                            d > 2500000 ? '#BD0026' :  // 10–20%
                            d > 1800000 ? '#E31A1C' :  // 20–30%
                            d > 1200000 ? '#FC4E2A' :  // 30–40%
                            d > 800000  ? '#FD8D3C' :  // 40–50%
                            d > 500000  ? '#FEB24C' :  // 50–60%
                            d > 300000  ? '#FED976' :  // 60–70%
                                            '#FFEDA0';   // ต่ำกว่า ~300K
                    }


                    function style(feature) {
                        return {
                            fillColor: getColor(feature.properties.p),
                            weight: 1,
                            opacity: 1,
                            color: 'white',
                            dashArray: '3',
                            fillOpacity: 0.7
                        };
                    }

                    function highlightFeature(e) {
                        var layer = e.target;
                        layer.setStyle({
                            weight: 5,
                            color: '#666',
                            dashArray: '',
                            fillOpacity: 0.7
                        });
                        if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                            layer.bringToFront();
                        }
                        info.update(layer.feature.properties);
                    }

                    function resetHighlight(e) {
                        geojson.resetStyle(e.target);
                        info.update();
                    }

                    function zoomToFeature(e) {
                        map.fitBounds(e.target.getBounds());
                    }

                    function onEachFeature(feature, layer) {
                        layer.on({
                            mouseover: highlightFeature,
                            mouseout: resetHighlight,
                            click: zoomToFeature
                        });






                        // --- STEP 1: สร้าง Array สำหรับเก็บทุกจังหวัด (ใช้ของ global ที่มีอยู่แล้ว)
                        var features = geojson_data.features;

                        // --- STEP 2: ฟังก์ชันหา Ranking
                        function getRanking(propertyName, provinceName) {
                            let sorted = features
                                .map(f => {
                                    let p = f.properties.popItem;
                                    return {
                                        name: f.properties.name,
                                        value: p ? p[propertyName] : 0
                                    };
                                })
                                .sort((a, b) => b.value - a.value);

                            // normalize string (ตัด space และทำเป็น lower case)
                            let target = provinceName.replace(/\s+/g, '').toLowerCase();

                            let idx = sorted.findIndex(f =>
                                f.name.replace(/\s+/g, '').toLowerCase() === target
                            );

                            return idx >= 0 ? idx + 1 : "-";
                        }


                        // ฟังก์ชันสำหรับแปลงชื่อ: เจอตัวใหญ่ → เติม space ข้างหน้า
                        function addSpaceBeforeUppercase(str) {
                            return str.replace(/([a-z])([A-Z])/g, '$1 $2');
                        }

                        // --- STEP 3: popup content
                        var p = feature.properties.popItem;
                        var provinceName = feature.properties.name;

                        var rawName = feature.properties.name;
                        var provinceName = addSpaceBeforeUppercase(rawName);



                        var rankTotal  = getRanking("ประชากรรวม (Total Population)", provinceName);
                        var rankMale   = getRanking("ประชากรชาย (Male population)", provinceName);
                        var rankFemale = getRanking("ประชากรหญิง (Female population)", provinceName);

                       var popupContent = `
                            <div id="popup-content" class="popup-card">
                                <h4 style="color: #2e2d2aff;">${provinceName}</h4>
                                <table>
                                    <tr>
                                        <td>
                                            <img src="images/3_peple.png" alt="Total" style="width:20px; vertical-align:middle; margin-right:5px;">
                                            Total
                                        </td>
                                        <td style="color: #0F4267;">
                                            ${p ? p["ประชากรรวม (Total Population)"].toLocaleString() : 0}
                                            <small style="color: #1e1e1eff;"> (Rank: ${rankTotal})</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="images/1_1_man.png" alt="Male" style="width:20px; vertical-align:middle; margin-right:5px;">
                                            Male
                                        </td>
                                        <td style="color: #78A3D4;">
                                            ${p ? p["ประชากรชาย (Male population)"].toLocaleString() : 0}
                                            <small style="color: #1e1e1eff;"> (Rank: ${rankMale})</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="images/2_1_woman.png" alt="Female" style="width:20px; vertical-align:middle; margin-right:5px;">
                                            Female
                                        </td>
                                        <td style="color: #FB6F92;">
                                            ${p ? p["ประชากรหญิง (Female population)"].toLocaleString() : 0}
                                            <small style="color: #1e1e1eff;"> (Rank: ${rankFemale})</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Density:</strong></td>
                                        <td>${p ? p["ความหนาแน่นต่อ ตร.กม. (Density per sq.km.)"] : 0} per km²</td>
                                    </tr>
                                </table>
                            </div>
                        `;

                        layer.bindPopup(popupContent);
                    }



                    var sourceInfo = L.control({position: 'bottomleft'});

                    sourceInfo.onAdd = function (map) {
                        var div = L.DomUtil.create('div', 'info');
                        div.innerHTML = '<small>Source: Department of Provincial Administration (DOPA), 2023</small>';
                        div.style.marginLeft = "20px";   // ✅ เพิ่มระยะห่างจากซ้าย
                        div.style.backgroundColor = "#eaeaea66";
                        div.style.fontSize = "12px";
                        return div;
                    };
                    sourceInfo.addTo(map);







                    
                    var geojson = L.geoJson(geojson_data, {
                        style: style,
                        onEachFeature: onEachFeature
                    }).addTo(map);

                    // legend
                    var legend = L.control({position: 'bottomright'});
                    legend.onAdd = function (map) {
                        var div = L.DomUtil.create('div', 'info legend'),
                            grades = [0, 300000, 500000, 800000, 1200000, 1800000, 2500000, 4000000],
                            labels = [],
                            from, to;

                        for (var i = 0; i < grades.length; i++) {
                            from = grades[i];
                            to = grades[i + 1];
                            labels.push(
                                '<i style="background:' + getColor(from + 1) + '"></i> ' +
                                from.toLocaleString() + (to ? '&ndash;' + to.toLocaleString() : '+')
                            );
                        }

                        div.innerHTML = labels.join('<br>');
                        return div;
                    };
                    legend.addTo(map);



                }); // end getJSON geojson

            }); // end getJSON population

        });


        
    </script>
</html>