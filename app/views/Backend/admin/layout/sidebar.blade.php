<aside id="sidebar">
    <nav id="navigation" class="collapse">
        <ul>
            <li @if(Request::segment(1)=='panel') class="active" @endif>
                <span title="Ayarlar">
                    <i class="icon-home"></i>
                    <span class="nav-title">Ayarlar</span>
                </span>
                <ul class="inner-nav">
                    <li @if(Request::segment(2)=='ayarlar') class="active" @endif><a href="{{ URL::to('panel/ayarlar') }}"><i class="icol-dashboard"></i> Ayarlar</a></li>
                </ul>
            </li>
            <li>
                <span title="Table">
                    <i class="icon-table"></i>
                    <span class="nav-title">Table</span>
                </span>
                <ul class="inner-nav">
                    <li><a href="tables.html"><i class="icol-style"></i> Static Tables</a></li>
                    <li><a href="responsive_tables.html"><i class="icol-hammer-screwdriver"></i> Responsive Tables</a></li>
                    <li><a href="data_tables.html"><i class="icol-table"></i> Data Tables</a></li>
                    <li><a href="detail_view.html"><i class="icol-eye"></i> Detail View Table</a></li>
                </ul>
            </li>
            <li>
                <span title="Statistic">
                    <i class="icon-graph"></i>
                    <span class="nav-title">Statistic</span>
                </span>
                <ul class="inner-nav">
                    <li><a href="statistic.html"><i class="icol-chart-curve"></i> Statistical Elements</a></li>
                    <li><a href="charts_gallery.html"><i class="icol-chart-pie"></i> Charts Gallery</a></li>
                </ul>
            </li>
            <li>
                <span title="Form">
                    <i class="icon-list"></i>
                    <span class="nav-title">Form</span>
                </span>
                <ul class="inner-nav">
                    <li><a href="form_layouts.html"><i class="icol-layout-select"></i> Form Layouts</a></li>
                    <li><a href="form_elements.html"><i class="icol-ui-text-field-password"></i> Form Elements</a></li>
                    <li><a href="form_wizard.html"><i class="icol-wand"></i> Form Wizard</a></li>
                    <li><a href="form_validation.html"><i class="icol-accept"></i> Form Validation</a></li>
                    <li><a href="form_cloning.html"><i class="icol-asterisk-orange"></i> Form Cloning</a></li>
                    <li><a href="wysiwyg.html"><i class="icol-pencil"></i> WYSIWYG</a></li>
                </ul>
            </li>
            <li>
                <span title="Elements">
                    <i class="icon-cogs"></i>
                    <span class="nav-title">Elements</span>
                </span>
                <ul class="inner-nav">
                    <li><a href="ui_bootstrap.html"><i class="icol-ui-tab-content"></i> Bootstrap Elements</a></li>
                    <li><a href="ui_components.html"><i class="icol-pipette"></i> Other Elements</a></li>
                    <li><a href="alerts.html"><i class="icol-error"></i> Alerts and Notifications</a></li>
                    <li><a href="boxes.html"><i class="icol-cog"></i> Widget Boxes</a></li>
                    <li><a href="buttons.html"><i class="icol-joystick"></i> Buttons</a></li>
                    <li><a href="file_uploader.html"><i class="icol-computer"></i> File Uploader</a></li>
                    <li><a href="file_manager.html"><i class="icol-databases"></i> File Manager</a>
                </ul>
            </li>
            <li>
                <span title="Extra">
                    <i class="icon-gift"></i>
                    <span class="nav-title">Extra</span>
                </span>
                <ul class="inner-nav">
                    <li><a href="profile.html"><i class="icol-user"></i> Profile Page</a></li>
                    <li><a href="mail.html"><i class="icol-email"></i> Mail Page</a></li>
                    <li><a href="invoice.html"><i class="icol-blog"></i> Invoice Page</a></li>
                    <li><a href="widgets.html"><i class="icol-ruby"></i> Custom Widgets</a></li>
                    <li><a href="gallery.html"><i class="icol-images"></i> Image Gallery</a>
                        <li><a href="contacts.html"><i class="icol-vcard"></i> Contact List</a></li>
                        <li><a href="404.html"><i class="icol-error"></i> Error Page (404)</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>

<div id="sidebar-separator"></div>