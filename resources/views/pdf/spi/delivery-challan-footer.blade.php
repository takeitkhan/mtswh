<div style="margin-bottom: 20px;">
    @if(isset($footerLogoPath) && file_exists($footerLogoPath))
        <div style="text-align: center; margin-bottom: 10px;">
            <img src="{{ $footerLogoPath }}" alt="Footer Logo" style="height: 40px;">
        </div>
    @endif
    
    <table style="width:100%; border-collapse: collapse;">
        <tr>
            <td style="width:33%; text-align: center; padding: 5px;">
                @if(isset($locationLogoPath))
                    <img src="{{ $locationLogoPath }}" alt="Location" style="width: 10px; height: 10px; margin-right: 3px;">
                @endif
                Address Info
            </td>
            <td style="width:34%; text-align: center; padding: 5px;">
                @if(isset($telephoneLogoPath))
                    <img src="{{ $telephoneLogoPath }}" alt="Phone" style="width: 10px; height: 10px; margin-right: 3px;">
                @endif
                +880-000-000000
            </td>
            <td style="width:33%; text-align: center; padding: 5px;">
                @if(isset($globeLogoPath))
                    <img src="{{ $globeLogoPath }}" alt="Web" style="width: 10px; height: 10px; margin-right: 3px;">
                @endif
                www.website.com
            </td>
        </tr>
    </table>
</div>
