<?php include('config.php'); ?>


<h3>Best Seller Handphone</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama</th>
            <th>Brand</th>
            <th>Jumlah Terjual</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "
            SELECT h.nama, h.brand, SUM(p.jumlah) as terjual 
            FROM penjualan p
            JOIN handphone h ON h.id = p.id_handphone
            GROUP BY h.id
            ORDER BY terjual DESC
            LIMIT 5
        ";
        $result = mysqli_query($koneksi, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['nama']}</td>
                    <td>{$row['brand']}</td>
                    <td>{$row['terjual']}</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>
