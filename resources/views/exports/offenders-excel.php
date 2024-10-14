<table>
    <thead>
        <tr>
            <th>Offense Name</th>
            <th>Male count</th>
            <th>Female count</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($offensesData as $offense): ?>
            <tr>
                <td>
                <?php echo $offense->type === 'Minor' ? $offense->minor_offense : $offense->major_offense; ?>

                </td>
                <td><?php echo $offense->male_count ?? 0; ?></td>
                <td><?php echo $offense->female_count ?? 0; ?></td>
                <td><?php echo ($offense->male_count ?? 0) + ($offense->female_count ?? 0); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>