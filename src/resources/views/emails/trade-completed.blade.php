<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>取引完了のお知らせ</title>
</head>
<body>
    <p>{{ $trade->seller->name }} 様</p>

    <p>出品した商品について、購入者が取引完了を行いました。</p>

    <p>【商品名】{{ $trade->item->name }}</p>
    <p>【購入者】{{ $trade->buyer->name }}</p>

    <p>マイページまたは取引画面からご確認ください。</p>

    <p>Free Market App</p>
</body>
</html>