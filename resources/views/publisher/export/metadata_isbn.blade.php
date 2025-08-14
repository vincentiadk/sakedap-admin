<table>
    <thead>
        <tr>
            <th >No</th>
            <th >Judul</th>
            <th >Penulis dan Kontributor Lainnya</th>
            <th >Tahun Terbit</th>
            <th >ISBN</th>
            <th >Deskripsi</th>
            <th >Preview</th>
            <th >Hak Akses</th>
            <th >Nama File</th>
        </tr>
    </thead>
    <tbody>
        @if(count($data) > 0)
            @foreach($data as $key => $d)
                <tr>
                    <td>
                        {{ $key + 1 }}
                    </td>
                    <td>
                        {{ $d['title'] }}
                    </td>
                    <td>
                        {{ $d['kepeng'] }}
                    </td>
                    <td>
                        {{ $d['tahun_terbit'] }}
                    </td>
                    <td>
                        {{ $d['isbnno'] }}
                    </td>
                     <td>
                        
                    </td>
                    <td>
                        1-10
                    </td>
                    <td>
                        2
                    </td>
                    <td>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="9" >Tidak Ada Data</td>
            </tr>
        @endif
    </tbody>
</table>
