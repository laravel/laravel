<?php
/**
 *    @package JAMA
 *
 *    For an m-by-n matrix A with m >= n, the QR decomposition is an m-by-n
 *    orthogonal matrix Q and an n-by-n upper triangular matrix R so that
 *    A = Q*R.
 *
 *    The QR decompostion always exists, even if the matrix does not have
 *    full rank, so the constructor will never fail.  The primary use of the
 *    QR decomposition is in the least squares solution of nonsquare systems
 *    of simultaneous linear equations.  This will fail if isFullRank()
 *    returns false.
 *
 *    @author  Paul Meagher
 *    @license PHP v3.0
 *    @version 1.1
 */
class PHPExcel_Shared_JAMA_QRDecomposition
{
    const MATRIX_RANK_EXCEPTION  = "Can only perform operation on full-rank matrix.";

    /**
     *    Array for internal storage of decomposition.
     *    @var array
     */
    private $QR = array();

    /**
     *    Row dimension.
     *    @var integer
     */
    private $m;

    /**
    *    Column dimension.
    *    @var integer
    */
    private $n;

    /**
     *    Array for internal storage of diagonal of R.
     *    @var  array
     */
    private $Rdiag = array();


    /**
     *    QR Decomposition computed by Householder reflections.
     *
     *    @param matrix $A Rectangular matrix
     *    @return Structure to access R and the Householder vectors and compute Q.
     */
    public function __construct($A)
    {
        if ($A instanceof PHPExcel_Shared_JAMA_Matrix) {
            // Initialize.
            $this->QR = $A->getArrayCopy();
            $this->m  = $A->getRowDimension();
            $this->n  = $A->getColumnDimension();
            // Main loop.
            for ($k = 0; $k < $this->n; ++$k) {
                // Compute 2-norm of k-th column without under/overflow.
                $nrm = 0.0;
                for ($i = $k; $i < $this->m; ++$i) {
                    $nrm = hypo($nrm, $this->QR[$i][$k]);
                }
                if ($nrm != 0.0) {
                    // Form k-th Householder vector.
                    if ($this->QR[$k][$k] < 0) {
                        $nrm = -$nrm;
                    }
                    for ($i = $k; $i < $this->m; ++$i) {
                        $this->QR[$i][$k] /= $nrm;
                    }
                    $this->QR[$k][$k] += 1.0;
                    // Apply transformation to remaining columns.
                    for ($j = $k+1; $j < $this->n; ++$j) {
                        $s = 0.0;
                        for ($i = $k; $i < $this->m; ++$i) {
                            $s += $this->QR[$i][$k] * $this->QR[$i][$j];
                        }
                        $s = -$s/$this->QR[$k][$k];
                        for ($i = $k; $i < $this->m; ++$i) {
                            $this->QR[$i][$j] += $s * $this->QR[$i][$k];
                        }
                    }
                }
                $this->Rdiag[$k] = -$nrm;
            }
        } else {
            throw new PHPExcel_Calculation_Exception(PHPExcel_Shared_JAMA_Matrix::ARGUMENT_TYPE_EXCEPTION);
        }
    }    //    function __construct()


    /**
     *    Is the matrix full rank?
     *
     *    @return boolean true if R, and hence A, has full rank, else false.
     */
    public function isFullRank()
    {
        for ($j = 0; $j < $this->n; ++$j) {
            if ($this->Rdiag[$j] == 0) {
                return false;
            }
        }
        return true;
    }    //    function isFullRank()

    /**
     *    Return the Householder vectors
     *
     *    @return Matrix Lower trapezoidal matrix whose columns define the reflections
     */
    public function getH()
    {
        for ($i = 0; $i < $this->m; ++$i) {
            for ($j = 0; $j < $this->n; ++$j) {
                if ($i >= $j) {
                    $H[$i][$j] = $this->QR[$i][$j];
                } else {
                    $H[$i][$j] = 0.0;
                }
            }
        }
        return new PHPExcel_Shared_JAMA_Matrix($H);
    }    //    function getH()

    /**
     *    Return the upper triangular factor
     *
     *    @return Matrix upper triangular factor
     */
    public function getR()
    {
        for ($i = 0; $i < $this->n; ++$i) {
            for ($j = 0; $j < $this->n; ++$j) {
                if ($i < $j) {
                    $R[$i][$j] = $this->QR[$i][$j];
                } elseif ($i == $j) {
                    $R[$i][$j] = $this->Rdiag[$i];
                } else {
                    $R[$i][$j] = 0.0;
                }
            }
        }
        return new PHPExcel_Shared_JAMA_Matrix($R);
    }    //    function getR()

    /**
     *    Generate and return the (economy-sized) orthogonal factor
     *
     *    @return Matrix orthogonal factor
     */
    public function getQ()
    {
        for ($k = $this->n-1; $k >= 0; --$k) {
            for ($i = 0; $i < $this->m; ++$i) {
                $Q[$i][$k] = 0.0;
            }
            $Q[$k][$k] = 1.0;
            for ($j = $k; $j < $this->n; ++$j) {
                if ($this->QR[$k][$k] != 0) {
                    $s = 0.0;
                    for ($i = $k; $i < $this->m; ++$i) {
                        $s += $this->QR[$i][$k] * $Q[$i][$j];
                    }
                    $s = -$s/$this->QR[$k][$k];
                    for ($i = $k; $i < $this->m; ++$i) {
                        $Q[$i][$j] += $s * $this->QR[$i][$k];
                    }
                }
            }
        }
        /*
        for($i = 0; $i < count($Q); ++$i) {
            for($j = 0; $j < count($Q); ++$j) {
                if (! isset($Q[$i][$j]) ) {
                    $Q[$i][$j] = 0;
                }
            }
        }
        */
        return new PHPExcel_Shared_JAMA_Matrix($Q);
    }    //    function getQ()

    /**
     *    Least squares solution of A*X = B
     *
     *    @param Matrix $B A Matrix with as many rows as A and any number of columns.
     *    @return Matrix Matrix that minimizes the two norm of Q*R*X-B.
     */
    public function solve($B)
    {
        if ($B->getRowDimension() == $this->m) {
            if ($this->isFullRank()) {
                // Copy right hand side
                $nx = $B->getColumnDimension();
                $X  = $B->getArrayCopy();
                // Compute Y = transpose(Q)*B
                for ($k = 0; $k < $this->n; ++$k) {
                    for ($j = 0; $j < $nx; ++$j) {
                        $s = 0.0;
                        for ($i = $k; $i < $this->m; ++$i) {
                            $s += $this->QR[$i][$k] * $X[$i][$j];
                        }
                        $s = -$s/$this->QR[$k][$k];
                        for ($i = $k; $i < $this->m; ++$i) {
                            $X[$i][$j] += $s * $this->QR[$i][$k];
                        }
                    }
                }
                // Solve R*X = Y;
                for ($k = $this->n-1; $k >= 0; --$k) {
                    for ($j = 0; $j < $nx; ++$j) {
                        $X[$k][$j] /= $this->Rdiag[$k];
                    }
                    for ($i = 0; $i < $k; ++$i) {
                        for ($j = 0; $j < $nx; ++$j) {
                            $X[$i][$j] -= $X[$k][$j]* $this->QR[$i][$k];
                        }
                    }
                }
                $X = new PHPExcel_Shared_JAMA_Matrix($X);
                return ($X->getMatrix(0, $this->n-1, 0, $nx));
            } else {
                throw new PHPExcel_Calculation_Exception(self::MATRIX_RANK_EXCEPTION);
            }
        } else {
            throw new PHPExcel_Calculation_Exception(PHPExcel_Shared_JAMA_Matrix::MATRIX_DIMENSION_EXCEPTION);
        }
    }
}
