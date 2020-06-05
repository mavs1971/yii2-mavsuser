<?php
	/**
	 * Created by PhpStorm.
	 * User: Abhimanyu
	 * Date: 20-02-2015
	 * Time: 13:58
	 */

	use mavs1971\user\models\Profile;
	use kartik\alert\AlertBlock;
	use kartik\grid\GridView;
        use kartik\select2;
	use yii\helpers\Html;
        use mavs1971\user\models\User;
        use yii\helpers\ArrayHelper;

	/** @var $this \yii\web\View */
	/** @var $dataProvider \abhimanyu\user\models\UserSearch */
	/** @var $searchModel \abhimanyu\user\models\UserSearch */

	$this->title = Yii::t('app', 'User Admin - ' . Yii::$app->name);

	echo AlertBlock::widget([
		                        'delay'           => 5000,
		                        'useSessionFlash' => TRUE
	                        ]);
?>
<div class="wbfactura-index">
<?= GridView::widget([
	                     'dataProvider' => $dataProvider,
	                     'filterModel'  => $searchModel,
	                     'columns'      => [
		                     ['class' => \kartik\grid\SerialColumn::className()],
		                    /* [
			                     'header' => '',
			                     'value'  => function ($model) {
				                     $avatar = Profile::findOne(['uid' => $model->id]);

				                     return '<div class="text-center">' . Html::img('@backend/../' . $avatar['avatar'], [
					                     'width' => 30,
					                     'alt'   => 'Profile Image'
				                     ]) . '</div>';
			                     },
			                     'format' => 'raw',
		                     ],*/
                                    'username',   
                                    [
                                    'class' => '\kartik\grid\DataColumn',
                                    'attribute' => 'email',
                                    'filterType' => GridView::FILTER_SELECT2,
                                    'filter' => ArrayHelper::map(user::find()->orderBy('email')->asArray()->all(), 'email', 'email'), 
                                    'filterWidgetOptions' => [
                                        'pluginOptions' => ['allowClear' => true],
                                        ],
                                    'filterInputOptions' => ['placeholder' => 'Any author', 'multiple' => true], // allows multiple authors to be chosen
                                    'format' => 'raw',              
                                     ],
		                     
		                     //'email',
		                     [
			                     'attribute' => 'super_admin',
			                     'value'     => function ($model) {
				                     if ($model->super_admin == 1)
					                     return '<div class="text-center text-success"><i class="glyphicon glyphicon-ok"></i></div>';
				                     else
					                     return '<div class="text-center text-danger"><i class="glyphicon glyphicon-remove"></i></div>';
			                     },
			                     'format'    => 'raw'
		                     ],
		                     [
			                     'header' => Yii::t('app','Status'),
			                     'value'  => function ($model) {
				                     return $model->isStatus;
			                     },
			                     'format' => 'raw'
		                     ],
                                     [
                                         'attribute' => 'datos.ACCION',

                                     ],
                                        [
                                         'attribute' => 'datos.NOMBRES',

                                     ],   
                                                     [
                                         'attribute' => 'datos.APELLIDOS',

                                     ],
		                     [
			                     'class'    => \kartik\grid\ActionColumn::className(),
			                     'template' => '{confirm} {block}',
			                     'buttons'  => [
				                     'confirm' => function ($url, $model) {
					                     if ($model->isConfirmed) {
						                     return Html::a('<i class="glyphicon glyphicon-ok"></i>', NULL);
					                     } else {
						                     return Html::a('<i class="glyphicon glyphicon-ok"></i>', $url, [
							                     'data-method'  => 'post',
							                     'data-confirm' => Yii::t('app', 'Are you sure you want to confirm this user?'),
							                     'title'        => Yii::t('app', 'Confirm User')
						                     ]);
					                     }
				                     },

				                     'block'   => function ($url, $model) {
					                     if ($model->isBlocked) {
						                     $title = Yii::t('app', 'Unblock User');
					                     } else {
						                     $title = Yii::t('app', 'Block User');
					                     }

					                     return Html::a('<i class="glyphicon glyphicon-lock"></i>', $url, [
						                     'data-method'  => 'post',
						                     'data-confirm' => Yii::t('app', 'Are you sure you want to block this user?'),
						                     'title'        => $title
					                     ]);
				                     }
			                     ]
		                     ]
	                     ],
                            'responsive'   => TRUE,
                            'hover'        => TRUE,
                            'condensed'    => TRUE,
                            'export'       => FALSE,
                            'panel'        => [
                                'heading' => 'LIstado de Socios Registrados',
                                'type'=>GridView::TYPE_INFO,
                                //'before'  => Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-primary'])
                           ],
                          'containerOptions'=>['style'=>'overflow: auto'], // only set when $responsive = false
                           'headerRowOptions'=>['class'=>'kartik-sheet-style'],
                           'pjax'=>true, // pjax is set to always true for this demo
                           // set your toolbar
                           
                     ]) ?>
    </div>
