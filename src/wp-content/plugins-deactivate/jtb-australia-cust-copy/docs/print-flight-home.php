<div style="background: url('https://secl-au-jtb.azurewebsites.net//images/DE/Agent/JTB/20190308044459197.jpg') center top no-repeat; background-size: cover;">
<div class="container" ng-app="DEASearchApp">
    <div class="row" ng-controller="DEASearchPnlCtrl" ng-init="DefaultItem='AIR'">
        <div id="DEACombinedSearchPanel" class="divDEACombinedSearchPanel" TargetServer="https://secl-au-jtb.azurewebsites.net" RouteURL="" AgentID="jtb" ng-init="data2.ServiceType='';EmbedLang='en-US';OpenTarget='_blank';Referrer=''">
            <div>
                <ul class="nav nav-pills searchPanelType" style="margin-left: 13px;">
                    <li id="searchPanelType-Air" ng-class="{'active': data.SelectedItem == 'AIR'}" ng-if="bEnableFlight && !bHideFlightItem">
                        <a id="searchPanelType-Air-tab" data-toggle="pill" href="#" ng-click="data.SelectedItem='AIR'">
                            <span ng-bind="getLabel('AirTicket')" class="ng-binding"></span>
                        </a>
                    </li>
                    <li id="searchPanelType-Htl" ng-class="{'active': data.SelectedItem == 'HTL'}" ng-if="bEnableHotel && !bHideHotelItem">
                        <a id="searchPanelType-Htl-tab" data-toggle="pill" href="#" ng-click="data.SelectedItem='HTL'">
                            <span ng-bind="getLabel('Hotel')" class="ng-binding"></span>
                        </a>
                    </li>
                    <li id="searchPanelType-Pkg" ng-class="{'active': data.SelectedItem == 'PKG'}" ng-if="bEnableDynamicPackage && !bHideFlightItem && !bHideHotelItem">
                        <a id="searchPanelType-Pkg-tab" data-toggle="pill" href="#" ng-click="data.SelectedItem='PKG'">
                            <span ng-bind="getLabel('FlightAndHotel')" class="ng-binding"></span>
                        </a>
                    </li>
                    <li id="searchPanelType-Svr" ng-class="{'active': data.SelectedItem == 'MIS'}" ng-if="bEnableOtherServices && !bHideServiceItem">
                        <a id="searchPanelType-Svr-tab" data-toggle="pill" href="#" ng-click="data.SelectedItem='MIS'">
                            <span ng-bind="getLabel('Misc')" ></span>
                        </a>
                    </li>
                    <li ng-repeat="MiscServiceType in MiscServiceTypeList" id="searchPanelType-SvrByType" ng-class="{'active': data.SelectedItem == 'MIS_' + MiscServiceType.Type}" ng-if="bEnableOtherServices && bMiscSearchPanelByType && !bHideServiceItem">
                        <a id="searchPanelType-Svr-tab" data-toggle="pill" href="#" ng-click="data.SelectedItem='MIS_' + MiscServiceType.Type;data2.ServiceType = MiscServiceType.Type">
                            <span ng-bind="::MiscServiceType.TypeName" ></span>
                        </a>
                    </li>
                    <li id="searchPanelType-Tour" ng-class="{'active': data.SelectedItem == 'TOUR'}" ng-if="bEnableTour && !bHideTourItem">
                        <a id="searchPanelType-Tour-tab" data-toggle="pill" href="#" ng-click="data.SelectedItem='TOUR'">
                            <span ng-bind="getLabel('Tour')" ></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content searchPanel">
                <div id="flight" ng-show="data.SelectedItem=='AIR'">
                    <div ng-include="'https://secl-au-jtb.azurewebsites.net/DesktopModules/DEAAirSearchResult/DEASearchPanel.min.html?ts=20190603053856' | trustAsResourceUrl" class="ng-scope">
                    </div>
                </div>
                <div id="hotel" ng-show="data.SelectedItem=='HTL'">
                    <div ng-include="'https://secl-au-jtb.azurewebsites.net/DesktopModules/DEAHotelSearchResult/DEAHotelSearchPanel.min.html?ts=20190603053856' | trustAsResourceUrl" class="ng-scope">
                    </div>
                </div>
                <div id="flightAndHotel" ng-show="data.SelectedItem=='PKG'">
                    <div ng-include="'https://secl-au-jtb.azurewebsites.net/DesktopModules/DEAPkgSearchResult/DEAPkgSearchPanel.min.html?ts=20190603053856' | trustAsResourceUrl" class="ng-scope">
                    </div>
                </div>
                <div id="misc" ng-show="data.SelectedItem.substring(0, 3)=='MIS'">
                    <div ng-include="'https://secl-au-jtb.azurewebsites.net/DesktopModules/DEAMiscSearchResult/DEAMiscSearchPanel.min.html?ts=20190603053856' | trustAsResourceUrl" class="ng-scope">
                    </div>
                </div>
                <div id="tour" ng-show="data.SelectedItem=='TOUR'">
                    <div ng-include="'https://secl-au-jtb.azurewebsites.net/DesktopModules/DEATourSearchResult/DEATourSearchPanel.min.html?ts=20190603053856' | trustAsResourceUrl" class="ng-scope"></div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>