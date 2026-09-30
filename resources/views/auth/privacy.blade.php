
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>隱私權保護政策 - 萬宇保全巡邏紀錄查詢系統</title>
    <link rel="shortcut icon" href="{{ asset('images/etuns.png') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        .privacy-content {
            line-height: 1.9;
            font-size: 16px;
            color: #444;
        }

        .privacy-content h5 {
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 15px;
            color: #333;
        }

        .privacy-content p {
            margin-bottom: 12px;
        }

        .privacy-content ul {
            padding-left: 25px;
        }

        .privacy-content li {
            margin-bottom: 8px;
        }

        .privacy-date {
            color: #777;
            font-size: 14px;
        }

        .privacy-footer {
            border-top: 1px solid #ddd;
            margin-top: 30px;
            padding-top: 20px;
        }
    </style>
</head>

<body>

<div class="head container-fluid" id="app">
    <nav class="navbar navbar-expand-md navbar-light">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ URL::asset('images/logo.png') }}" class="img-fluid">
        </a>
    </nav>
</div>

<div class="container">
    <div class="row justify-content-center mt-5 mb-5">
        <div class="col-md-10">

            <div class="card">
                <div class="card-header">
                    萬宇資訊管理平台隱私權保護政策
                </div>

                <div class="card-body privacy-content">

                    <p class="privacy-date">
                        最後更新日期：中華民國 115 年 9 月 30 日
                    </p>

                    <p>
                        萬宇資訊管理平台由萬宇保全股份有限公司（以下簡稱本公司）管理，
                        提供客戶及員工使用。本政策說明您使用本平台網站及 APP 時，
                        個人資料的蒐集、處理、利用及保護方式。
                    </p>

                    <h5>一、適用範圍</h5>

                    <p>
                        本政策適用於本公司管理之資訊管理平台網站及 APP，
                        不適用於非本公司管理之外部網站或服務。
                        若透過本平台連結至外部網站或服務，
                        其個人資料處理方式，依該網站或服務之隱私權政策辦理。
                    </p>


                    <h5>二、個人資料的蒐集與用途</h5>

                    <p>
                        本平台依使用者身分及使用功能，於必要範圍內蒐集下列資料：
                    </p>

                    <p><strong>（一）客戶使用</strong></p>

                    <p>
                        為提供登入驗證及授權查詢服務，
                        可能蒐集帳號、姓名、聯絡方式及操作紀錄。
                        客戶可查閱授權範圍內的巡邏紀錄及異常狀況照片。
                    </p>

                    <p><strong>（二）員工使用</strong></p>

                    <p>
                        員工登入帳號及初始密碼由本公司建立後提供，
                        供員工使用本平台。
                        相關帳號資料用於身分驗證、權限管理及資訊安全維護。
                    </p>

                    <p>
                        為辦理人事、出勤及勤務管理，
                        可能蒐集員工姓名、聯絡方式、身分證正反面影像、
                        個人照片（大頭照）、學歷證明、薪資轉帳帳戶，
                        以及上下班打卡紀錄、請假資料、巡邏掃描紀錄、
                        異常狀況照片、打卡與巡邏時的 GPS 位置資訊、
                        公司公告的閱覽與確認紀錄，
                        以及透過手機簽署回傳之文件、電子簽名及簽署紀錄。
                    </p>

                    <p>
                        本公司為履行職業安全衛生法及勞工健康保護規則所定義務，
                        於必要範圍內蒐集、處理及利用員工到職體格檢查報告
                        （體檢表）及依法應辦理之在職健康檢查資料，
                        供勞工健康管理、工作適性評估及法定紀錄保存使用，
                        不作為健康管理目的以外之用途。
                    </p>

                    <p>
                        體格及健康檢查資料僅供經授權之健康管理承辦人員、
                        提供勞工健康服務之醫護人員，
                        以及依法有權機關於必要範圍內使用，
                        不提供客戶查閱。
                        員工未提供依法必要之檢查資料時，
                        可能影響本公司完成法定健康管理及工作安排。
                    </p>

                    <p><strong>（三）系統使用紀錄</strong></p>

                    <p>
                        為維持平台運作及資訊安全，
                        可能記錄 IP 位址、裝置資訊、登入時間及系統操作紀錄。
                    </p>

                    <p>
                        除上述體格及健康檢查資料依其專屬用途使用外，
                        其餘資料用於身分驗證、人事與勤務管理、客戶查詢、
                        公告傳達與確認、文件簽署、聯繫回覆、契約履行及資訊安全，
                        並於蒐集目的及法令允許的範圍內處理、利用。
                    </p>

                    <p>
                        您可選擇不提供非依法必要之個人資料，
                        但可能因此無法使用需該資料才能提供的功能或服務。
                        個人資料權利及申請方式，依本政策第六點及第八點辦理。
                    </p>


                    <h5>三、資料利用與保存</h5>

                    <p>
                        個人資料由本公司、您所屬或接受服務之企業，
                        以及受委託的資訊服務廠商，
                        在業務必要及授權範圍內，以電子或紙本方式處理、利用。
                        個人資料之利用及儲存地區為臺灣。
                    </p>

                    <p>
                        本平台依使用者身分及業務需求設定存取權限。
                        客戶僅能查閱授權範圍內的巡邏紀錄及異常照片；
                        員工及管理人員依其職務與權限使用相關資料。
                        體格及健康檢查資料另依第二點所列用途及對象限制存取。
                    </p>

                    <p>
                        個人資料於服務、契約或蒐集目的所需期間，
                        以及法令規定的保存期限內保存。
                        體格及健康檢查資料依勞工健康保護相關法令規定之期限保存。
                        保存原因消失且無其他法定或業務必要之保存依據後，
                        依法刪除、銷毀或以無法識別個人之方式處理。
                    </p>


                    <h5>四、資料保護與第三人提供</h5>

                    <p>
                        本公司採取必要的資訊安全防護、身分驗證及權限控管措施，
                        僅允許經授權人員存取個人資料，
                        並要求受委託廠商遵守保密及個人資料保護義務。
                    </p>

                    <p>
                        本公司不出售或出租您的個人資料。
                        除下列情形外，不向第三人揭露：
                    </p>

                    <ul>
                        <li>經您同意。</li>
                        <li>依法律規定，或配合有權機關的合法要求。</li>
                        <li>於法令允許範圍內，為履行契約或提供服務所必要。</li>
                        <li>為避免生命、身體、自由或財產上的危險，依法有提供之必要。</li>
                    </ul>

                    <p>
                        因平台維運或服務需求委託廠商處理個人資料時，
                        本公司將要求其於委託範圍內使用，
                        並依法履行監督義務。
                    </p>

                    <p>
                        體格及健康檢查資料之處理、利用及提供，
                        應遵守個人資料保護法及勞工健康保護相關法令，
                        並限於第二點所列用途及依法必要之範圍，
                        不因上述情形而擴大利用範圍。
                    </p>


                    <h5>五、Cookie 及 APP 權限</h5>

                    <p>
                        網站可能使用 Cookie 維持登入狀態及提供必要功能。
                        您可透過瀏覽器設定限制或拒絕 Cookie，
                        但部分功能可能無法正常使用。
                    </p>

                    <p>APP 依實際使用功能，可能請求下列權限：</p>

                    <ul>
                        <li>
                            <strong>相機：</strong>
                            用於需透過相機進行的巡邏掃描、異常狀況拍照及文件拍攝上傳。
                        </li>

                        <li>
                            <strong>照片或檔案存取：</strong>
                            用於上傳異常照片、員工個人資料、證明文件、
                            體檢表及簽署後之文件。
                        </li>

                        <li>
                            <strong>定位：</strong>
                            用於記錄上下班打卡及巡邏作業的 GPS 位置資訊。
                        </li>

                        <li>
                            <strong>通知：</strong>
                            用於接收公司公告、勤務及相關服務通知。
                        </li>
                    </ul>

                    <p>
                        部分文件提供手機簽署及回傳功能，
                        本平台將保存電子簽名、簽署文件及相關紀錄，
                        供文件確認、行政管理及契約履行使用。
                    </p>

                    <p>
                        您可透過裝置設定管理上述權限；
                        拒絕或關閉權限，可能影響對應功能的使用。
                    </p>


                    <h5>六、您的個人資料權利</h5>

                    <p>
                        您可透過本政策所列聯絡方式，
                        就您的個人資料申請：
                    </p>

                    <ul>
                        <li>查詢或閱覽。</li>
                        <li>取得複製本。</li>
                        <li>補充或更正。</li>
                        <li>停止蒐集、處理或利用。</li>
                        <li>刪除。</li>
                    </ul>

                    <p>
                        本公司將確認您的身分並依法辦理。
                        若依法仍須保存或處理相關資料，
                        將向您說明原因。
                    </p>


                    <h5>七、政策修訂</h5>

                    <p>
                        本公司得因平台功能、服務內容或法令變更修訂本政策。
                        修訂後將於本平台公告，並更新最後更新日期。
                    </p>


                    <h5>八、聯絡方式</h5>

                    <p>
                        如對本政策有疑問，或需行使個人資料權利，
                        請透過以下方式聯絡本公司：
                    </p>

                    <div class="privacy-footer">
                        <p>
                            <strong>公司名稱：</strong>
                            萬宇保全股份有限公司
                        </p>

                        <p>
                            <strong>聯絡電話：</strong>
                            06-3565597
                        </p>

                        <p>
                            <strong>電子郵件：</strong>
                            <a href="mailto:etun.group@etun.com.tw">
                                etun.group@etun.com.tw
                            </a>
                        </p>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            返回
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
